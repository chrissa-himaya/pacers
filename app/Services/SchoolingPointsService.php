<?php

namespace App\Services;

use App\Models\Schooling;
use App\Models\Sourcedata;
use App\Models\Assignment;
use Carbon\Carbon;

/**
 * SchoolingPointsService
 * ─────────────────────────────────────────────────────────────────────────────
 * Replicates the Excel LET / XLOOKUP formula for PPAD scoring:
 *
 *   =LET(
 *     G,          [@rating],
 *     M,          [@total_students],
 *     N,          [@standing],
 *     loc,        [@school_location],
 *     half_pt,    XLOOKUP(category, ppad[PPD], ppad[CPT_factor1]),
 *     foreign_pt, XLOOKUP(category, ppad[PPD], ppad[CPT_foreignpt]),
 *     flat_pt,    XLOOKUP(category, ppad[PPD], ppad[flat]),
 *     IF(cat="Civil Service Eligibility", IF(G>0, flat_pt, 0),
 *     IF(cat="Specialization Course",     flat_pt,
 *     IF(cat IN ["Graduate","Post Graduate"], flat_pt,
 *     IF(loc="local",   ((G/100)*half_pt) + (((M-N+1)/M)*half_pt),
 *     IF(loc="foreign", foreign_pt, 0))))))
 *
 * sourcedatas column mapping:
 *   min_month  → half_pt    (factor1 — used in both halves of the local formula)
 *   max_month  → max_month  (informational only, not used in formula)
 *   max_point  → foreign_pt (flat points for foreign schools)
 *   min_point  → flat_pt    (flat points for Civil Service / Graduate / Specialization)
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * HOW THE CONTROLLER CALLS THIS SERVICE
 * ─────────────────────────────────────────────────────────────────────────────
 * SchoolingsController passes an ARRAY to computeForRank():
 *
 *   $svcInput = [
 *       'assignment_id'   => $data['assignment_id'],
 *       'school_location' => $request->input('school_location'),  // 'local' | 'foreign'
 *       'rating'          => $data['rating'],
 *       'standing'        => $data['standing'],
 *       'total_students'  => $data['total_student'],   // ← note: key is total_students
 *   ];
 *   $points = $svc->computeForRank($svcInput, $rankId);   // $rankId = 1..6
 *
 * The zero-out logic for ranks below rank_during_completion is handled
 * by the controller's loop — this service just returns the raw formula value.
 *
 * computeAndSave() / computeAllRanks() accept a Schooling model and are used
 * for bulk recomputation (e.g. recomputeForOfficer).
 * ─────────────────────────────────────────────────────────────────────────────
 */
class SchoolingPointsService
{
    // ── Constants ──────────────────────────────────────────────────────────────

    const RANK_COLUMNS = ['2LT', '1LT', 'CPT', 'MAJ', 'LTC', 'COL'];

    /** ranks.id ↔ rank code */
    const RANK_IDS = [
        '2LT' => 1,
        '1LT' => 2,
        'CPT' => 3,
        'MAJ' => 4,
        'LTC' => 5,
        'COL' => 6,
    ];

    /** schoolings column name per rank code */
    const RANK_DB_COLS = [
        '2LT' => 'seclt',
        '1LT' => 'firstlt',
        'CPT' => 'cpt',
        'MAJ' => 'maj',
        'LTC' => 'ltc',
        'COL' => 'col',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // PRIMARY API  — used by SchoolingsController (store / update / computePoints)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Compute the schooling point for ONE rank using the input ARRAY
     * that the controller builds and passes in.
     *
     * This is the exact signature the controller uses:
     *   foreach ([1=>'seclt', 2=>'firstlt', ...] as $rankId => $col) {
     *       $data[$col] = $svc->computeForRank($svcInput, $rankId);
     *   }
     *
     * @param  array{
     *     assignment_id:   int|string|null,
     *     school_location: string|null,
     *     rating:          float|string|null,
     *     standing:        int|string|null,
     *     total_students:  int|string|null,
     * } $input
     * @param  int $rankId  ranks.id  (1 = 2LT … 6 = COL)
     * @return float
     */
    public function computeForRank(array $input, int $rankId): float
    {
        $assignmentId = isset($input['assignment_id'])
            ? (int) $input['assignment_id']
            : null;

        if (! $assignmentId) {
            return 0.0;
        }

        // Look up the source row for (assignment_id, rank_id)
        $source = Sourcedata::where('assignment_id', $assignmentId)
            ->where('rank_id', $rankId)
            ->first();

        if (! $source) {
            return 0.0;
        }

        $category = $this->getCategoryName($assignmentId);
        $location = strtolower(trim((string) ($input['school_location'] ?? 'local')));

        $rating   = (float) ($input['rating']         ?? 0);
        $total    = (int)   ($input['total_students']  ?? 0);   // key: total_students
        $standing = (int)   ($input['standing']        ?? 0);

        return $this->applyFormula(
            category:  $category,
            location:  $location,
            rating:    $rating,
            total:     $total,
            standing:  $standing,
            halfPt:    (float) $source->min_month,   // factor1 / half_pt
            foreignPt: (float) $source->max_point,   // CPT_foreignpt
            flatPt:    (float) $source->min_point,   // flat for special categories
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MODEL-BASED API  — used for bulk / background recomputation
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Compute points for all 6 rank columns from a Schooling model,
     * persist them, and return the refreshed model.
     */
    public function computeAndSave(Schooling $schooling): Schooling
    {
        $points = $this->computeAllRanks($schooling);

        $schooling->update([
            'seclt'   => $points['2LT'],
            'firstlt' => $points['1LT'],
            'cpt'     => $points['CPT'],
            'maj'     => $points['MAJ'],
            'ltc'     => $points['LTC'],
            'col'     => $points['COL'],
        ]);

        return $schooling->fresh();
    }

    /**
     * Compute all 6 rank columns from a Schooling model.
     * Ranks below rank_during_completion receive 0.
     *
     * Internally bridges to computeForRank(array) so both paths
     * share exactly the same formula logic.
     *
     * @return array<string, float>  ['2LT' => float, '1LT' => float, …]
     */
    public function computeAllRanks(Schooling $schooling): array
    {
        $result = [];

        $rankDuringComp = strtoupper(trim($schooling->rank_during_completion ?? ''));
        $rankOrder      = array_flip(self::RANK_COLUMNS);   // rank => 0-based index
        $compIdx        = $rankOrder[$rankDuringComp] ?? null;

        // Build the same array the controller would pass
        $svcInput = [
            'assignment_id'   => $schooling->assignment_id,
            'school_location' => $schooling->schoolingunits?->location ?? 'local',
            'rating'          => $schooling->rating,
            'standing'        => $schooling->standing,
            'total_students'  => $schooling->total_student,  // map DB column → array key
        ];

        foreach (self::RANK_COLUMNS as $rank) {
            $rankId = self::RANK_IDS[$rank] ?? null;

            // Zero ranks before rank_during_completion
            if ($compIdx !== null && $rankOrder[$rank] < $compIdx) {
                $result[$rank] = 0.0;
                continue;
            }

            if (! $rankId) {
                $result[$rank] = 0.0;
                continue;
            }

            $result[$rank] = $this->computeForRank($svcInput, $rankId);
        }

        return $result;
    }

    /**
     * Bulk recompute all schooling records for one officer.
     */
    public function recomputeForOfficer(string $pmCode): void
    {
        Schooling::where('pm_code', $pmCode)
            ->with('schoolingunits')
            ->get()
            ->each(fn($s) => $this->computeAndSave($s));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CORE FORMULA
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Applies the Excel LET formula for a single rank row.
     *
     * Local regular course:
     *   ((rating / 100) × halfPt) + (((total − standing + 1) / total) × halfPt)
     *
     * Foreign regular course:
     *   foreignPt  (flat)
     *
     * Civil Service Eligibility:
     *   flatPt if rating > 0, else 0
     *
     * Graduate Course / Post Graduate Course:
     *   flatPt  (flat)
     *
     * Specialization Course:
     *   flatPt  (flat)
     */
    public function applyFormula(
        string $category,
        string $location,
        float  $rating,
        int    $total,
        int    $standing,
        float  $halfPt,
        float  $foreignPt,
        float  $flatPt,
    ): float {
        // ── Special categories ──────────────────────────────────────────────
        if ($category === 'Civil Service Eligibility') {
            return $rating > 0 ? $flatPt : 0.0;
        }

        if ($category === 'Specialization Course') {
            return $flatPt;
        }

        if (in_array($category, ['Graduate Course', 'Post Graduate Course'], true)) {
            return $flatPt;
        }

        // ── Regular military courses ────────────────────────────────────────
        if ($location === 'local') {
            if ($total <= 0 || $standing <= 0) {
                return 0.0;
            }

            $ratingComponent   = ($rating / 100) * $halfPt;
            $standingComponent = (($total - $standing + 1) / $total) * $halfPt;

            return round($ratingComponent + $standingComponent, 5);
        }

        if ($location === 'foreign') {
            return $foreignPt;
        }

        return 0.0;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Resolve the category name from assignment_id (request-scoped static cache).
     */
    protected function getCategoryName(int $assignmentId): string
    {
        static $cache = [];

        if (! isset($cache[$assignmentId])) {
            $assignment            = Assignment::find($assignmentId);
            $cache[$assignmentId]  = $assignment?->name ?? '';
        }

        return $cache[$assignmentId];
    }
}