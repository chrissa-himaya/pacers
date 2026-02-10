<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Sourcedata;
use Illuminate\Support\Facades\Log;

class SchoolingPointsService
{
    /*
    |--------------------------------------------------------------------------
    | Excel Formula Reference (per rank column: CPT, MAJ, LTC, COL)
    |--------------------------------------------------------------------------
    |
    | =LET(
    |   cat,       [@category],
    |   loc,       [@[school_location]],
    |   G,         [@rating],
    |   M,         [@[total_students]],
    |   N,         [@standing],
    |   half_pt,   XLOOKUP([@category], ppad[...], ppad[{RANK}_factor1]),
    |   foreign_pt,XLOOKUP([@category], ppad[...], ppad[{RANK}_foreignpt]),
    |   civil_service, source!${COL}$45,       ← fixed cell per rank column
    |   post_grad,     source!${COL}$46,       ← fixed cell per rank column
    |   specialization,source!${COL}$47,       ← fixed cell per rank column
    |
    |   IF(cat="Civil Service Eligibility", IF(G>0, civil_service, 0),
    |     IF(cat="Specialization Course", specialization,
    |       IF(cat="Graduate Course", post_grad,
    |         IF(loc="local",  ((G/100)*half_pt) + (((M-N+1)/M)*half_pt),
    |           IF(loc="foreign", foreign_pt, 0))))))
    |
    |--------------------------------------------------------------------------
    | Database Mapping (sourcedatas → rankpoints)
    |--------------------------------------------------------------------------
    |
    | Excel Variable   | sourcedata FK              | rankpoints.name
    | -----------------|-----------------------------|------------------
    | half_pt          | min_month_rankpoint_id      | factor1
    | (unused factor2) | min_point_rankpoint_id      | factor2
    | specialization   | max_month_rankpoint_id      | maxpt
    | civil_service    | max_month_rankpoint_id      | maxpt
    | post_grad        | max_month_rankpoint_id      | maxpt
    | foreign_pt       | max_point_rankpoint_id      | foreignpt
    |
    */

    private const SOURCE_RELATIONS = [
        'minMonthRankpoint:id,points',   // factor1 → half_pt
        'minPointRankpoint:id,points',   // factor2 (unused in main formula)
        'maxMonthRankpoint:id,points',   // maxpt → civil_service / specialization / post_grad
        'maxPointRankpoint:id,points',   // foreignpt → foreign_pt
    ];

    /**
     * Compute schooling points for a specific target rank.
     *
     * @param  array  $data    Form data: assignment_id, school_location, rating, total_students, standing
     * @param  int    $rankId  Target rank (1=2LT, 2=1LT, 3=CPT, 4=MAJ, 5=LTC, 6=COL)
     * @return float
     */
    public function computeForRank(array $data, int $rankId): float
    {
        $assignmentId = (int) ($data['assignment_id'] ?? 0);
        $loc          = strtolower(trim((string) ($data['school_location'] ?? '')));
        $G            = (float) ($data['rating'] ?? 0);
        $M            = (int) ($data['total_students'] ?? 0);
        $N            = (int) ($data['standing'] ?? 0);

        Log::debug('SchoolingPoints::computeForRank input', [
            'assignment_id' => $assignmentId,
            'rank_id'       => $rankId,
            'location'      => $loc,
            'rating'        => $G,
            'total_students'=> $M,
            'standing'      => $N,
        ]);

        if ($assignmentId <= 0 || $rankId <= 0) {
            return 0.0;
        }

        // Verify assignment belongs to type 5 (Professional Preparation & Development)
        $assignment = Assignment::select('id', 'name', 'type_id')->find($assignmentId);
        if (!$assignment || (int) $assignment->type_id !== 5) {
            return 0.0;
        }

        $category = strtolower(trim($assignment->name));

        // Pre-Entry Course: no points (Excel shows dashes across all ranks)
        if ($category === 'pre-entry course') {
            return 0.0;
        }

        // ─── Look up sourcedata ────────────────────────────────────
        $source = $this->findSourcedata($assignmentId, $rankId, $category);

        if (!$source) {
            Log::warning("SchoolingPoints: No sourcedata for assignment_id={$assignmentId}, rank_id={$rankId}, category={$category}");
            return 0.0;
        }

        // Extract values (matching Excel variable names)
        $half_pt    = (float) optional($source->minMonthRankpoint)->points;  // factor1
        $foreign_pt = (float) optional($source->maxPointRankpoint)->points;  // foreignpt
        $maxpt      = (float) optional($source->maxMonthRankpoint)->points;  // maxpt

        Log::debug('SchoolingPoints: resolved values', [
            'sourcedata_id' => $source->id,
            'half_pt'       => $half_pt,
            'foreign_pt'    => $foreign_pt,
            'maxpt'         => $maxpt,
            'category'      => $category,
        ]);

        // ═══════════════════════════════════════════════════════════
        //  FLAT-POINT CATEGORIES (Excel: fixed cell references)
        //  civil_service = maxpt, specialization = maxpt, post_grad = maxpt
        // ═══════════════════════════════════════════════════════════

        if ($category === 'civil service eligibility') {
            // Excel: IF(G > 0, civil_service, 0)
            return ($G > 0) ? round($maxpt, 6) : 0.0;
        }

        if ($category === 'specialization course') {
            // Excel: specialization (always returned, no condition)
            return round($maxpt, 6);
        }

        if ($category === 'graduate course' || $category === 'post graduate course' || $category === 'undergraduate course') {
            // Excel: post_grad (always returned, no condition)
            return round($maxpt, 6);
        }

        // ═══════════════════════════════════════════════════════════
        //  STANDARD CATEGORIES (OBC, OAC, CGSC, SOC)
        //  Excel: IF(loc="local", formula, IF(loc="foreign", foreign_pt, 0))
        // ═══════════════════════════════════════════════════════════

        if ($loc === 'foreign') {
            return round($foreign_pt, 6);
        }

        if ($loc === 'local') {
            if ($half_pt == 0) {
                return 0.0;
            }
            if ($M <= 0 || $N <= 0 || $N > $M) {
                return 0.0;
            }

            // Excel: ((G/100)*half_pt) + (((M-N+1)/M)*half_pt)
            $ratingPart   = ($G / 100.0) * $half_pt;
            $standingPart = (($M - $N + 1) / $M) * $half_pt;

            return round($ratingPart + $standingPart, 6);
        }

        return 0.0;
    }

    /**
     * Find sourcedata for a given assignment + rank.
     *
     * Strict match only: requires both assignment_id AND rank_id to match.
     * If no sourcedata exists for a specific rank, returns null (0 points).
     * All computation is strictly dependent on sourcedata configuration.
     */
    private function findSourcedata(int $assignmentId, int $rankId, string $category): ?Sourcedata
    {
        // Try exact match first (assignment + rank)
        $source = Sourcedata::query()
            ->where('assignment_id', $assignmentId)
            ->where('rank_id', $rankId)
            ->with(self::SOURCE_RELATIONS)
            ->first();

        if ($source) {
            return $source;
        }

        // No fallback — if no sourcedata exists for this exact assignment + rank, return null.
        // Points are strictly dependent on sourcedata configuration.

        return null;
    }
}