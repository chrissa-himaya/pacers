<?php

// app/Services/AssignmentHistoryPointsService.php
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

        $rankId = Rank::where('code', $h->rank_during_completion)->value('id');
        if (!$rankId) return 0.0;

        $src = Sourcedata::query()
            ->with(['minMonthRankpoint', 'minPointRankpoint', 'maxMonthRankpoint', 'maxPointRankpoint'])
            ->where('assignment_id', $h->assignment_id)
            ->where('rank_id', $rankId)
            ->first();

        if (!$src->minMonthRankpoint || !$src->minPointRankpoint || !$src->maxMonthRankpoint || !$src->maxPointRankpoint) {
            return 0.0;
        }

        $minMonths = (float) optional($src->minMonthRankpoint)->points;
        $maxMonths = (float) optional($src->maxMonthRankpoint)->points;
        $minPoints = (float) optional($src->minPointRankpoint)->points;
        $maxPoints = (float) optional($src->maxPointRankpoint)->points;

        if ($minMonths <= 0 && $maxMonths <= 0) return 0.0;

        $months = (float) $h->year_earned * 12.0;

        // default clamp + linear interpolation
        if ($months <= $minMonths) return round($minPoints, 4);
        if ($months >= $maxMonths) return round($maxPoints, 4);
        if ($maxMonths <= $minMonths) return round($minPoints, 4);

        $t = ($months - $minMonths) / ($maxMonths - $minMonths);
        return round($minPoints + ($maxPoints - $minPoints) * $t, 4);
    }

    public function recomputeAndSave(AssignmentHistory $h): void
    {
        $h->computed_points = $this->compute($h);
        $h->points_last_recomputed_at = now();
        $h->saveQuietly(); // avoids observer loops
    }
}

