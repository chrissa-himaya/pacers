<?php

namespace App\Jobs;

use App\Models\AssignmentHistory;
use App\Models\Rank;
use App\Services\AssignmentHistoryPointsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecomputeAssignmentHistoryPointsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $assignmentId,
        public int $rankId
    ) {}

    public function handle(AssignmentHistoryPointsService $svc): void
    {
        $rankCode = Rank::where('id', $this->rankId)->value('code');

        $q = AssignmentHistory::query()
            ->where('assignment_id', $this->assignmentId);

        // Because assignment_histories stores rank as string code
        if ($rankCode) {
            $q->where('rank_during_completion', $rankCode);
        }

        $q->chunkById(500, function ($rows) use ($svc) {
            foreach ($rows as $h) {
                $svc->recomputeAndSave($h);
            }
        });
    }
}
