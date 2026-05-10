<?php

namespace App\Jobs;

use App\Models\BillPeriod;
use App\Models\User;
use App\Services\BillGenerationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateBillsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300; // 5 minutes
    public int $tries   = 1;   // no auto-retry — generation must be deterministic

    public function __construct(
        public readonly BillPeriod $period,
        public readonly int $triggeredBy
    ) {}

    public function handle(BillGenerationService $service): void
    {
        try {
            $stats = $service->generateForPeriod($this->period);

            activity('billing')
                ->performedOn($this->period)
                ->causedBy(User::find($this->triggeredBy))
                ->event('bills_generated')
                ->withProperties($stats)
                ->log("Bills generated for period '{$this->period->name}'.");
        } catch (\Throwable $e) {
            Log::error("GenerateBillsJob failed for period {$this->period->id}", [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $e): void
    {
        // Reset status so the user can retry
        $this->period->refresh();
        if ($this->period->status === 'generating') {
            $this->period->update(['status' => 'open']);
        }
    }
}
