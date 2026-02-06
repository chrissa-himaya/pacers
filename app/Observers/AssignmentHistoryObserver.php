<?php

namespace App\Observers;

use App\Models\AssignmentHistory;
use App\Services\AssignmentHistoryPointsService;

class AssignmentHistoryObserver
{
    /**
     * Trigger after CREATE or UPDATE of assignment_histories row
     */
    public function saved(AssignmentHistory $history): void
    {
        // Prevent infinite loop when we save computed_points
        if ($history->wasChanged(['computed_points','points_last_recomputed_at'])) {
            return;
        }

        app(AssignmentHistoryPointsService::class)
            ->recomputeAndSave($history);
    }
}
