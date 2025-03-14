<?php

namespace App\Jobs;
use App\Services\ThirdPartyApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
class ProcessClickupTeamAndWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $accessToken;
    protected $userId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $accessToken, int $userId)
    {
        $this->accessToken = $accessToken;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $apiService = new ThirdPartyApiService([
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json',
        ]);

        // Fetch the user's team (workspace)
        $teams = $apiService->get('https://api.clickup.com/api/v2/team');

        if (empty($teams['teams'])) {
            Log::error("No ClickUp workspace found for user {$this->userId}");
            return;
        }

        // Get the first available workspace ID
        $teamId = $teams['teams'][0]['id'];

        // Fetch existing webhooks
        $webhooks = $apiService->get("https://api.clickup.com/api/v2/team/{$teamId}/webhook");

        if (!empty($webhooks['webhooks'])) {
            Log::info("ClickUp webhook already exists for team {$teamId}");
            return;
        }

        // Register new webhook
        $webhookData = [
            'endpoint' => route('clickup.webhook'),
            'events' => ['taskUpdated', 'taskCreated','taskDeleted','taskPriorityUpdated','taskStatusUpdated','taskAssigneeUpdated','taskDueDateUpdated',
            'taskTagUpdated','taskMoved','taskCommentPosted','taskCommentUpdated','taskTimeEstimateUpdated','taskTimeTrackedUpdated'],
        ];

        $newWebhook = $apiService->post("https://api.clickup.com/api/v2/team/{$teamId}/webhook", $webhookData);

        if (!empty($newWebhook['id'])) {
            Log::info("Webhook created successfully for team {$teamId}");
        } else {
            Log::error("Failed to create webhook for team {$teamId}");
        }
    }
}
