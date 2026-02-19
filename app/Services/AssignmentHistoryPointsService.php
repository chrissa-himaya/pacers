<?php

namespace App\Services;

use App\Models\AssignmentHistory;
use App\Models\Rank;
use App\Models\Sourcedata;

class AssignmentHistoryPointsService
{
    public function compute(AssignmentHistory $h): float
    {
        if (!$h->assignment_id) return 0.0;
        if ($h->year_earned === null) return 0.0;
        if (!$h->rank_during_completion) return 0.0;

        // Map rank code (e.g., "CPT") -> rank_id
        $rankId = Rank::where('code', $h->rank_during_completion)->value('id');
        if (!$rankId) return 0.0;

        // Pull the rule row directly
        $src = Sourcedata::query()
            ->where('assignment_id', $h->assignment_id)
            ->where('rank_id', $rankId)
            ->first();

        if (!$src) return 0.0;

        // Direct columns (no relationships)
        $minMonths = (float) $src->min_month;
        $maxMonths = (float) $src->max_month;
        $minPoints = (float) $src->min_point;
        $maxPoints = (float) $src->max_point;

        // Basic validation
        if ($maxMonths <= 0 || $maxPoints < 0) return 0.0;

        // Excel uses INT(12*years) => floor
        $months = (int) floor(((float) $h->year_earned) * 12.0);

        // ===== Excel behavior =====
        // months < minMon => 0
        if ($months < $minMonths) return 0.0;

        // months >= maxMon => maxPts
        if ($months >= $maxMonths) return round($maxPoints, 4);

        // Linear between min and max
        if ($maxMonths <= $minMonths) return round($minPoints, 4); // avoid divide by zero

        $slope = ($maxPoints - $minPoints) / ($maxMonths - $minMonths);
        $raw = $minPoints + (($months - $minMonths) * $slope);

        return round(max(0.0, $raw), 4);
    }

    public function recomputeAndSave(AssignmentHistory $h): void
    {
        $h->computed_points = $this->compute($h);
        $h->points_last_recomputed_at = now();
        $h->saveQuietly();
    }
}
