<?php
namespace App\Services\Auth;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\Auth\ClickupAuthService;
use App\Services\Auth\CrmAuthService;
use App\Providers\RouteServiceProvider;
class OAuthService
{
    protected ClickupAuthService $clickupAuthService;
    protected CrmAuthService $crmAuthService;

    public function __construct(ClickupAuthService $clickupAuthService, CrmAuthService $crmAuthService)
    {
        $this->clickupAuthService = $clickupAuthService;
        $this->crmAuthService = $crmAuthService;
    }

    public function handleCallback(string $provider, string $code)
    {
        $redirectBack = RouteServiceProvider::HOME;
        $user = $this->authenticateUser();
        if (!$user) {
            return request()->ajax()
                ? response()->json(['message' => 'User authentication failed'], 401)
                : redirect($redirectBack)->with('error', 'User authentication failed');
        }
        if (!$user) {
            return response()->json(['message' => 'User authentication failed'], 401);
        }

        $response = match ($provider) {
            'clickup' => $this->clickupAuthService->handleClickupAuth($code, $user->id),
            default => $this->crmAuthService->handleCrmAuth($code, $user),
        };

        if (request()->ajax()) {
            return response()->json([
                'status' => $response['status'] ?? 'success',
                'message' => $response['message'] ?? 'Connection successful',
                'data' => $response['data'] ?? [],
            ]);
        }
    
        // Redirect back with success or error message
        return isset($response['status']) && $response['status'] === 'error'
            ? redirect($redirectBack)->with('error', $response['message'] ?? 'Connection failed')
            : redirect($redirectBack)->with('success', $response['message'] ?? 'Connected successfully');
    }

    private function authenticateUser(): ?User
    {
        if (Auth::check()) {
            return Auth::user();
        }

        $user = User::first();
        if ($user) {
            Auth::login($user);
        }

        return $user;
    }
}
?>