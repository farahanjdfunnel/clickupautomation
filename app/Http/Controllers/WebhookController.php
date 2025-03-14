<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppStatusNotification;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use stdClass;

class WebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        // Validate webhook payload
        $payload = $request->all();

        if (in_array($payload['type'], ['INSTALL', 'UNINSTALL'])) {
            try
            {
                sleep(10);
                $user = User::where('location_id',$payload['locationId'])->first();
                if(!$user)
                {
                    return true ; //ignore;
                }
                if($user && $user->name != 'Location User')
                {
                    $payload['name'] = $user->name;
                }
                else
                {
                    $user = User::updateLocationInfo($user);
                    $payload['name'] = $user->name;
                }
                Mail::to(config('mail.admin_email'))->send(new AppStatusNotification($payload));
                return response()->json(['message' => 'Webhook processed successfully'], 200);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Failed to process webhook'], 500);
            }
        }
        if($payload['type']  == 'Void')
        {
            
        }

        return response()->json(['message' => 'Webhook received but no action taken'], 200);
    }
}
