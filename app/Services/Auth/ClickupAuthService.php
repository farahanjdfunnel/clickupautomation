<?php
namespace App\Services\Auth;

use App\Exceptions\OAuthException;
use App\Helper\CRM;
use App\Services\ThirdPartyApiService;

class ClickupAuthService
{
    protected $apiService;
    public function __construct()
    {
        $this->apiService = new ThirdPartyApiService();
    }
    public function handleClickupAuth(string $code, int $userId)
    {
        $response = $this->apiService->post('https://api.clickup.com/api/v2/oauth/token', [
            'client_id' => env('CLICKUP_CLIENT_ID'),
            'client_secret' => env('CLICKUP_CLIENT_SECRET'),
            'code' => $code,
        ]);

        if (!isset($response['access_token'])) {
            throw new OAuthException('Invalid ClickUp token', 400);
        }

        return CRM::saveClickupToken($response['access_token'], $userId);
    }
}

?>