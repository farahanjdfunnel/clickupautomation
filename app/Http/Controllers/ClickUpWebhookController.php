<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class ClickUpWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        // Log the incoming request
        Log::info('ClickUp Webhook Received', ['data' => $request->all()]);

        // Validate webhook data
        $event = $request->input('event');
        $taskId = $request->input('task_id');
        $workspaceId = $request->input('team_id');

        if (!$event || !$taskId) {
            return response()->json(['error' => 'Invalid webhook payload'], 200);
        }

        // Process different event types
        switch ($event) {
            case 'taskCreated':
                Log::info("Task Created in Workspace {$workspaceId}: Task ID - {$taskId}");
                break;

            case 'taskUpdated':
                Log::info("Task Updated in Workspace {$workspaceId}: Task ID - {$taskId}");
                break;

            default:
                Log::warning("Unhandled ClickUp event: {$event}");
        }

        // Respond to ClickUp
        return response()->json(['message' => 'Webhook received']);
    }
}
