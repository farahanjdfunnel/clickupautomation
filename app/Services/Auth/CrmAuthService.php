<?php
namespace App\Services\Auth;

use App\Helper\CRM;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Jobs\CreateUserAccount;

class CrmAuthService
{
    public function handleCrmAuth(string $code, User $user)
    {
        $tokenResponse = CRM::crm_token($code, '');
        $tokenData = json_decode($tokenResponse);

        if (!isset($tokenData->userType)) {
            return response()->json(['message' => 'Invalid CRM response'], 400);
        }

        $userType = strtolower($tokenData->userType);
        $mainRedirect = route('location.dashboard');

        [$connected, $con] = CRM::go_and_get_token($tokenData, '', $user->id, $user->crmauth ?? null);

        if ($connected) {
            $newUser = User::find($con->user_id);
            Auth::login($newUser);
            CreateUserAccount::dispatch($newUser)->onQueue(config('app.job_queue'));

            if ($userType === 'company') {
                return redirect(route('admin.setting'))->with('success', 'Connected Successfully');
            }

            return redirect($mainRedirect)->with('success', 'Connected Successfully');
        }

        return $userType === 'company'
            ? response()->json(['message' => 'Unable to connect to the company'], 400)
            : redirect($mainRedirect)->with('error', json_encode($tokenData));
    }
}

?>