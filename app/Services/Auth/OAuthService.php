<?php
namespace App\Services\Auth;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\Auth\ClickupAuthService;
use App\Services\Auth\CrmAuthService;

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
        $user = $this->authenticateUser();
        if (!$user) {
            return response()->json(['message' => 'User authentication failed'], 401);
        }

        return match ($provider) {
            'clickup' => $this->clickupAuthService->handleClickupAuth($code, $user->id),
            default => $this->crmAuthService->handleCrmAuth($code, $user),
        };
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