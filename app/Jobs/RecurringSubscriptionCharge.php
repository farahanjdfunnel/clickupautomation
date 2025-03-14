<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Services\SubscriptionChargeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RecurringSubscriptionCharge implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $maxExceptions = 3;

    public function handle(SubscriptionChargeService $chargeService)
    {
        //chunk by id fetch to closuere where itself
        $subscriptions = Subscription::dueForCharge()->get();
        foreach ($subscriptions as $subscription) {
            try {
                // call job and insdie job call service
                $success = $chargeService->chargeSubscription($subscription);

                if (!$success) {
                    Log::warning('Failed to charge subscription', [
                        'subscription_id' => $subscription->id
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Error processing subscription charge', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}
