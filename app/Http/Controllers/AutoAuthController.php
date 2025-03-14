<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\CreateUserAccount;
use App\Models\CrmAuths;
use App\Models\User;
use App\Services\PaymentProviderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutoAuthController extends Controller
{

    protected const VIEW = 'autoauth';
    public function index()
    {
        return view('admin.setting.index');
    }
    public function oauth($type = "company", $id = "12")
    {
        if (empty($id)) {
            return "";
        }
        return view('admin.oauth.connect', get_defined_vars());
    }
    public function oAuthDisconnect(Request $request, $provider)
    {
        $user = \Auth::user();
        if (!$user) {
            exit;
        }
        if ($provider == 'crmagency') {
            CrmAuths::where('user_id', $user->id)->delete();
        }
        return redirect()->back()->with('success', 'Disconnected');
    }
    public function authChecking(Request $req)
    {

        if ($req->ajax()) {
            if ($req->has('location') && $req->has('token')) {
                $location = $req->location;
                $user = User::with('crmauth')->where('location_id', $req->location)->first();
                if (!$user) {
                    $user = new User();
                    $user->name = 'Location User';
                    //$user->first_name = 'User';
                    //$user->last_name = 'User';
                    $user->email = $location . '@presave.net';
                    $user->password = bcrypt('presave_' . $location);
                    $user->dob = '1998-08-14';
                    $user->avatar = 'images/avatar-1.jpg';
                    $user->location_id = $location;
                    $user->ghl_api_key = '-';
                    $user->save();
                }
                $user->ghl_api_key = $req->token;
                $user->save();
                request()->merge(['user_id' => $user->id]);
                session([
                    'location_id' => $user->location_id,
                    'uid' => $user->id,
                    'user_id' => $user->id,
                    'user_loc' => $user->location_id,
                ]);

                $res = new \stdClass;
                $res->user_id = $user->id;
                $res->location_id = $user->location_id ?? null;
                $res->is_crm = false;
                request()->user_id = $user->id;
                $res->token = $user->ghl_api_key;
                $token = $user->crmauth;
                $res->crm_connected = false;
                if ($token) {
                    // request()->code = $token;
                    list($tokenx, $token) = \CRM::go_and_get_token($token->refresh_token, 'refresh', $user->id, $token);
                    $res->crm_connected = $tokenx && $token;
                }
                if (!$res->crm_connected) {
                    $res->crm_connected = \CRM::ConnectOauth($req->location, $res->token, false, $user->id);
                }
                if ($res->crm_connected) {
                    if (\Auth::check()) {
                        \Auth::logout();
                        sleep(1);
                    }
                    \Auth::login($user);
                }
                $response = PaymentProviderService::configureIfNotPresent($user);
                if ($response['success']) {
                    $res->is_crm = $res->crm_connected;
                    $res->token_id = encrypt($res->user_id);
                    return response()->json($res);
                }
            }
        }
        return response()->json(['status' => 'invalid request']);
    }

    public function connect()
    {

        return view(self::VIEW . '.connect');
    }

    public function authError()
    {
        return view(self::VIEW . '.error');
    }
    public function crmCallback(Request $request)
    {
        $code = $request->code ?? null;
        if ($code) {
            $user_id = null;
            if (auth()->check()) {
                $user = loginUser(); //auth user
                $user_id = $user->id;
            } else {
                $user = User::first();
                Auth::login($user);
                $user_id = $user->id;
            }
            $code = \CRM::crm_token($code, '');
            $code = json_decode($code);
            $user_type = $code->userType ?? 'company';
            $main = route('location.dashboard');
            if ($user_type) {
                $token = $user->crmauth ?? null;
                list($connected, $con) = \CRM::go_and_get_token($code, '', $user_id, $token);
                if ($connected) {
                    $user_id = $con->user_id;
                    $user = User::find($user_id);
                    Auth::login($user);
                    CreateUserAccount::dispatch($user)->onQueue(config('app.job_queue'));
                    if (strtolower($user_type) == 'company') {
                        $main = route('admin.setting');
                        return redirect($main)->with('success', 'Connected Successfully');
                    }
                    return redirect($main)->with('success', 'Connected Successfully');
                }
                if (strtolower($user_type) == 'company') {
                    return response()->json(['message' => 'Unable to connect to the company']);
                }
                return redirect($main)->with('error', json_encode($code));
            }
            return response()->json(['message' => 'Not allowed to connect']);
        }
    }
}
