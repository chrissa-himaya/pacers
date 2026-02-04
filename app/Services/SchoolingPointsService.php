<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Sourcedata;

class SchoolingPointsService
{
    public function computeForRank(array $data, int $rankId): float
    {
        $assignmentId = (int) ($data['assignment_id'] ?? 0);
        $loc = strtolower(trim((string) ($data['school_location'] ?? '')));
        $G = (float) ($data['rating'] ?? 0);
        $M = (int) ($data['total_students'] ?? 0);
        $N = (int) ($data['standing'] ?? 0);

        if ($assignmentId <= 0 || $rankId <= 0)
            return 0.0;

        $assignment = Assignment::select('id', 'type_id')->find($assignmentId);
        if (!$assignment || (int) $assignment->type_id !== 5)
            return 0.0;

        $source = Sourcedata::query()
            ->where('assignment_id', $assignmentId)
            ->where('rank_id', $rankId)
            ->with([
                'minMonthRankpoint:id,points',   
                'maxPointRankpoint:id,points',  
            ])
            ->first();

        if (!$source)
            return 0.0;

        $halfPt = (float) optional($source->minMonthRankpoint)->points;  
        $foreignPt = (float) optional($source->maxPointRankpoint)->points; 

        if ($loc === 'foreign') {
            return round($foreignPt, 4);
        }

        if ($loc === 'local') {
            if ($M <= 0 || $N <= 0 || $N > $M)
                return 0.0;

            $ratingPart = ($G / 100.0) * $halfPt;
            $standingPart = (($M - $N + 1) / $M) * $halfPt;

            return round($ratingPart + $standingPart, 4);
        }

        return 0.0;
    }
}
