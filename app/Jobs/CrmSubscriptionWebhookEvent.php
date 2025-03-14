<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class CrmSubscriptionWebhookEvent implements ShouldQueue
{
    use Queueable;

    public $event;
    public $payload;
    public $webhookUrl;

    /**
     * Create a new job instance.
     *
     * @param string $event
     * @param array $payload
     * @param string $webhookUrl
     */
    public function __construct(string $event, array $payload, string $webhookUrl)
    {
        $this->event = $event;
        $this->payload = $payload;
        $this->webhookUrl = $webhookUrl;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Send the POST request to the webhook endpoint
            $response = Http::post($this->webhookUrl, $this->payload);
            // Check if the request was successful
            if ($response->successful()) {
                Log::info('Webhook event sent successfully', [
                    'event' => $this->event,
                    'payload' => $this->payload,
                ]);
            } else {
                // Handle failure or retry if necessary
                Log::error('Failed to send webhook event', [
                    'event' => $this->event,
                    'payload' => $this->payload,
                    'status_code' => $response->status(),
                    'response' => $response->body(),
                ]);
            }
        } catch (Exception $e) {
            // Handle exceptions (e.g., network issues)
            Log::error('Error sending webhook event', [
                'event' => $this->event,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
