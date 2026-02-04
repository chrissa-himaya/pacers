<?php


namespace App\Services;


use App\Models\Assignment;
use App\Models\Sourcedata;


class SchoolingPointsService
{
    /**
     * Compute points using Excel-like logic.
     *
     * Expected keys:
     * - assignment_id
     * - school_location ("local"|"foreign")
     * - rating (0-100)
     * - standing (1..total_students)
     * - total_students
     */
    public function compute(array $data): float
    {
        $assignmentId = (int) ($data['assignment_id'] ?? 0);
        $loc = strtolower(trim((string) ($data['school_location'] ?? '')));
        $G = (float) ($data['rating'] ?? 0);
        $M = (int) ($data['total_students'] ?? 0);
        $N = (int) ($data['standing'] ?? 0);


        if ($assignmentId <= 0)
            return 0.0;


        $assignment = Assignment::select('id', 'type_id')->find($assignmentId);
        if (!$assignment)
            return 0.0;


        // If you only want to apply this formula to PPAD (type_id=5):
        if ((int) $assignment->type_id !== 5) {
            return 0.0;
        }


        $source = Sourcedata::where('assignment_id', $assignmentId)
            ->with([
                'minMonthRankpoint:id,points',
                'maxPointRankpoint:id,points',
            ])
            ->first();


        if (!$source)
            return 0.0;


        // ---- mapping (edit here if you want a different mapping)
        $halfPt = (float) optional($source->minMonthRankpoint)->points; // factor1 / half_pt
        $foreignPt = (float) optional($source->maxPointRankpoint)->points; // foreignpt


        // Excel: IF(loc="foreign", foreign_pt, IF(loc="local", rating+standing, 0))
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