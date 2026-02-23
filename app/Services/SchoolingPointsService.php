<?php

namespace App\Services;

use App\Models\Schooling;
use App\Models\Sourcedata;
use App\Models\Assignment;

/**
 * SchoolingPointsService
 * ─────────────────────────────────────────────────────────────────────────────
 * Replicates the Excel LET / XLOOKUP formula for PPAD scoring.
 *
 * Excel formula (per rank column):
 *
 *   =LET(
 *     cat,        [@category],
 *     loc,        [@school_location],
 *     G,          [@rating],
 *     M,          [@total_students],
 *     N,          [@standing],
 *     half_pt,    XLOOKUP(cat, ppad[PPD], ppad[{RANK}_factor1]),
 *     foreign_pt, XLOOKUP(cat, ppad[PPD], ppad[{RANK}_foreignpt]),
 *     civil_service, source!${COL}$45,
 *     post_grad,     source!${COL}$46,
 *     specialization,source!${COL}$47,
 *     IF(cat="Civil Service Eligibility", IF(G>0, civil_service, 0),
 *     IF(cat="Specialization Course",     specialization,
 *     IF(cat="Graduate Course",           post_grad,
 *     IF(loc="local",  ((G/100)*half_pt)+(((M-N+1)/M)*half_pt),
 *     IF(loc="foreign", foreign_pt, 0))))))
 *
 * sourcedatas column mapping:
 *   min_month → half_pt    (factor1)
 *   max_month → flat_pt    (maxpt: civil_service / post_grad / specialization)
 *   max_point → foreign_pt (foreignpt)
 *   min_point → (unused)
 *
 * Each category requires one sourcedata row per rank_id.
 * If no row exists for a given (assignment_id, rank_id), that rank returns 0.
 *
 * All category strings are normalized to lowercase internally.
 * ─────────────────────────────────────────────────────────────────────────────
 */
class SchoolingPointsService
{
    // ── Rank configuration ────────────────────────────────────────────────────

    const RANK_COLUMNS = ['2LT', '1LT', 'CPT', 'MAJ', 'LTC', 'COL'];

    const RANK_IDS = [
        '2LT' => 1,
        '1LT' => 2,
        'CPT' => 3,
        'MAJ' => 4,
        'LTC' => 5,
        'COL' => 6,
    ];

    const RANK_DB_COLS = [
        '2LT' => 'seclt',
        '1LT' => 'firstlt',
        'CPT' => 'cpt',
        'MAJ' => 'maj',
        'LTC' => 'ltc',
        'COL' => 'col',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // PRIMARY API
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Compute the schooling point for ONE rank.
     *
     * Called by SchoolingsController for each of the 6 rank columns:
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
     * @param  int $rankId  ranks.id (1=2LT … 6=COL)
     * @return float
     */
    public function computeForRank(array $input, int $rankId): float
    {
        $assignmentId = (int) ($input['assignment_id'] ?? 0);

        if ($assignmentId <= 0) {
            return 0.0;
        }

        $category = $this->getCategoryName($assignmentId);

        if ($category === '' || $category === 'pre-entry course') {
            return 0.0;
        }

        $source = Sourcedata::where('assignment_id', $assignmentId)
            ->where('rank_id', $rankId)
            ->first();

        if (! $source) {
            return 0.0;
        }

        return $this->applyFormula(
            category:  $category,
            location:  strtolower(trim((string) ($input['school_location'] ?? 'local'))),
            rating:    (float) ($input['rating']         ?? 0),
            total:     (int)   ($input['total_students']  ?? 0),
            standing:  (int)   ($input['standing']        ?? 0),
            halfPt:    (float) ($source->min_month ?? 0),  // factor1
            foreignPt: (float) ($source->max_point ?? 0),  // foreignpt
            flatPt:    (float) ($source->max_month ?? 0),  // maxpt
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MODEL-BASED API  (bulk / background recomputation)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Compute all 6 rank columns from a Schooling model, persist, and return.
     */
    public function computeAndSave(Schooling $schooling): Schooling
    {
        $schooling->update(
            collect($this->computeAllRanks($schooling))
                ->mapWithKeys(fn($pts, $rank) => [self::RANK_DB_COLS[$rank] => $pts])
                ->all()
        );

        return $schooling->fresh();
    }

    /**
     * Compute all 6 rank columns from a Schooling model.
     * Ranks below rank_during_completion receive 0.
     *
     * @return array<string, float>  e.g. ['2LT' => 0.0, 'CPT' => 2.7, ...]
     */
    public function computeAllRanks(Schooling $schooling): array
    {
        $rankOrder      = array_flip(self::RANK_COLUMNS);
        $rankDuringComp = strtoupper(trim($schooling->rank_during_completion ?? ''));
        $compIdx        = $rankOrder[$rankDuringComp] ?? null;

        $svcInput = [
            'assignment_id'   => $schooling->assignment_id,
            'school_location' => $schooling->schoolingunits?->location ?? 'local',
            'rating'          => $schooling->rating,
            'standing'        => $schooling->standing,
            'total_students'  => $schooling->total_student,
        ];

        $result = [];
        foreach (self::RANK_COLUMNS as $rank) {
            // Zero out ranks before rank_during_completion
            if ($compIdx !== null && $rankOrder[$rank] < $compIdx) {
                $result[$rank] = 0.0;
                continue;
            }

            $result[$rank] = $this->computeForRank($svcInput, self::RANK_IDS[$rank]);
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
     * Apply the Excel LET formula for a single (category, rank) combination.
     *
     * Category handling (all compared lowercase):
     *   "civil service eligibility" → flatPt if rating > 0, else 0
     *   "specialization course"     → flatPt always
     *   "graduate course"           → flatPt always
     *   "post graduate course"      → flatPt always
     *   "undergraduate course"      → flatPt always
     *   everything else (local)     → ((G/100)*halfPt) + (((M-N+1)/M)*halfPt)
     *   everything else (foreign)   → foreignPt
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
        $category = strtolower(trim($category));

        return match (true) {

            $category === 'civil service eligibility'
                => $rating > 0 ? round($flatPt, 6) : 0.0,

            in_array($category, [
                'specialization course',
                'graduate course',
                'post graduate course',
                'undergraduate course',
            ], true)
                => round($flatPt, 6),

            $location === 'foreign'
                => round($foreignPt, 6),

            // Local formula: both guards must pass
            $location === 'local'
                && $halfPt > 0
                && $total > 0
                && $standing > 0
                && $standing <= $total
                => round(
                    ($rating / 100 * $halfPt) + (($total - $standing + 1) / $total * $halfPt),
                    6
                ),

            default => 0.0,

        };
    }

    // ─────────────────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Resolve assignment name → lowercase, request-scoped static cache.
     */
    protected function getCategoryName(int $assignmentId): string
    {
        static $cache = [];

        return $cache[$assignmentId] ??= strtolower(
            trim(Assignment::find($assignmentId)?->name ?? '')
        );
    }
}