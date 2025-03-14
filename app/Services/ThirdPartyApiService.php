<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Exceptions\OAuthException;

class ThirdPartyApiService
{
    protected $headers = [];

    public function __construct(array $headers = [])
    {
        $this->headers = $headers;
    }

    /**
     * Make a GET request.
     */
    public function get(string $url, array $params = [])
    {
        return $this->sendRequest('GET', $url, $params);
    }

    /**
     * Make a POST request.
     */
    public function post(string $url, array $data = [])
    {
        return $this->sendRequest('POST', $url, $data);
    }

    /**
     * Handle the API request.
     */
    protected function sendRequest(string $method, string $url, array $data = [])
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->timeout(10)
                ->$method($url, $data);

            $responseData = $response->json();

            if (!$response->successful()) {
                Log::error("API Request Failed", [
                    'url' => $url,
                    'method' => $method,
                    'data' => $data,
                    'response' => $responseData
                ]);

                throw new OAuthException($responseData['error'] ?? 'Unknown error', $response->status());
            }

            return $responseData;
        } catch (\Exception $e) {
            Log::error("API Request Exception", ['message' => $e->getMessage(), 'url' => $url]);
            throw new OAuthException($e->getMessage(), 500);
        }
    }
}
?>