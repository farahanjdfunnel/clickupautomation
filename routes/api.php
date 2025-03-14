<?php

use App\Http\Controllers\WebhookController;
use App\Jobs\CreateUserAccount;
use App\Jobs\ProcessRefreshToken;
use App\Jobs\RecurringSubscriptionCharge;
use App\Jobs\RegisterPaymentProvider;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


//http://127.0.0.1:8000/api/register-payment-provider?id=2

Route::get('/register-payment-provider', function (Request $request) {
    $user =User::find($request->id);
    RegisterPaymentProvider::dispatch($user)->onQueue(config('app.job_queue'));
    return response()->json([
        'message' => 'Job dispatched successfully!'
    ], 200);
});
Route::get('/sync-location-name', function () {
    $users = User::whereNotNull('location_id')->get();
    foreach($users as $user)
    {
        CreateUserAccount::dispatch($user)->onQueue(config('app.job_queue'));
    }
 
    return response()->json([
        'message' => 'Job dispatched successfully!'
    ], 200);
});

Route::any('/installation-verification', function (Request $request) {
$token = $request->private_token;
if($token == 'eci-ezpay-test')
{
        return response()->json([
            'success' => true
        ], 200);
}
    return response()->json([
        'success' => false
    ], 400);
    

});
Route::any('/process-ipospays-webhook', function (Request $request) {

    $eventType = $request->eventType??null;
    $subEventType = $request->subEventType??null;
    if($eventType == 'Settlement' && $subEventType == 'ClosedBatch')
    {
        $transactions = $request->settlementTxnDetails??[];
        foreach($transactions as $trans)
        {
            $transaction = Transaction::where("ipospays_transaction_id", $trans['transactionId'])->first();
            if($transaction)
            {
                try {
                    $apiKey = get_option($transaction->user_id, 'api_key', '');
                    $event = "payment.captured";
                    $subscriptionData = [
                        'event' => $event,
                        'ghlTransactionId' => $transaction->crm_transaction_id ?? null,
                        'chargeId' => $transaction->ref_id ?? null,
                        "chargeSnapshot" => [
                            "status" => 'succeeded',
                            "chargeId" =>  $transaction->ref_id,
                            "chargedAt" => $transaction->created_at->timestamp
                        ],
                        'locationId' => $transaction->location_id ?? null,
                        'apiKey' => $apiKey,
                    ];
                    $subscriptionService = new SubscriptionService();
                    $subscriptionService->triggerSubscriptionEvent($subscriptionData);
                    $transaction->transaction_status = 'succeeded';
                    $transaction->save();
                } catch (\Throwable $th) {
                    //throw $th;
                    Log::info('ErrorPostApi', [$th->getMessage()]);
                }
            }
           
        }
    }
   Log::info("Process IposPays Webhook",(array)$request->all());
});

Route::get('/cron-jobs/process-refresh-token', function () {
    dispatch((new ProcessRefreshToken()));
});

Route::get('/process-recurring-payment', function () {
    dispatch((new RecurringSubscriptionCharge()));
});

Route::post('/process-crm-webhook', [WebhookController::class, 'handleWebhook'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]); // Disable CSRF for this route