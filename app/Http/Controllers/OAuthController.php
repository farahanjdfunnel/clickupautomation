<?php

namespace App\Http\Controllers;

use App\Services\Auth\OAuthService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Middleware\ThrottleRequests;

class OAuthController extends Controller
{
    protected OAuthService $oauthService;

    public function __construct(OAuthService $oauthService)
    {
        $this->middleware(ThrottleRequests::class . ':5,1')->only('callback'); // Max 5 requests per minute
        $this->oauthService = $oauthService;
    }
    public function redirectToClickUp(Request $request)
    {
        $clientId = config('services.clickup.client_id');
        $redirectUri = config('services.clickup.redirect');
        $authUrl = "https://app.clickup.com/api?client_id={$clientId}&redirect_uri={$redirectUri}";
        if ($request->ajax()) {
            return response()->json(['url' => $authUrl]);
        }
        return redirect()->away($authUrl);
    }
    public function callback(Request $request, string $provider)
    {
        $code = $request->input('code');

        if (!$code) {
            return response()->json(['message' => 'Authorization code missing'], 400);
        }

        return $this->oauthService->handleCallback($provider, $code);
    }

    public function disconnect()
    {
        $user = auth()->user();

        if ($user->clickupauth) {
            $user->clickupauth()->delete();
        }

        return redirect()->back()->with('success', 'Disconnected from ClickUp successfully.');
    }
}
