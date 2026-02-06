<?php

namespace App\Observers;

use App\Jobs\RecomputeAssignmentHistoryPointsJob;
use App\Models\Sourcedata;

class SourcedataObserver
{
    public function saved(Sourcedata $s): void
    {
        if ($s->assignment_id && $s->rank_id) {
            RecomputeAssignmentHistoryPointsJob::dispatch($s->assignment_id, $s->rank_id);
        }
    }

    public function deleted(Sourcedata $s): void
    {
        if ($s->assignment_id && $s->rank_id) {
            RecomputeAssignmentHistoryPointsJob::dispatch($s->assignment_id, $s->rank_id);
        }
    }
}
