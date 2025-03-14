<?php

namespace App\Http\Controllers;

use App\DTOs\PaymentRequestDTO;
use App\Helper\CRM;
use App\Helper\gCache;
use App\Jobs\CreateTransactionAndSubscriptionJob;
use App\Models\SPIn;
use App\Models\Transaction;
use App\Models\User;
use App\Services\SubscriptionDateCalculator;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Contracts\PaymentProcessorInterface;
use App\DTOs\SpinPaymentRequestDTO;
use Carbon\Carbon;

class CustomPaymentProviderController extends Controller
{
    protected $spin_url;
    protected $hpp_url;
   
    public function __construct(private readonly PaymentProcessorInterface $paymentProcessor,)
    {
        $environment = config('ipospay.environment');
        $this->spin_url = config("ipospay.{$environment}.spin_url");
        $this->hpp_url = config("ipospay.{$environment}.hpp_url");
    }
    public function paymentProdivderPaymentUrl(Request $request)
    {
        return view('paymentprovider.init');
    }
  
    
  
    
   
    public function spinTrigger(Request $request)
    {

        $type = 'Sale';
        if ($request->amount <= 0) {
            $type = "Auth";
        }
       
        $ref_id = generateTransactionRefId(8);

        $location_id = $request->locationId ?? null;
        $tpn = $request->tpn;
        $authKey = $request->authKey;
        $cacheKey = 'user_location_' . $request->locationId;

        $user = gCache::remember($cacheKey, 60, function () use ($location_id) {
            return User::where('location_id', $location_id)->first();
        });
        $cacheSpinKey = 'spin_' . $request->locationId . 'tpn_'.$request->tpn;
        $spin = gCache::remember($cacheSpinKey, 60, function () use ($location_id, $tpn , $authKey) {
            return SPIn::where(['location_id' =>  $location_id, 'auth_key' => $authKey, 'tpn' => $tpn])->first();
        });
        $environment  = $spin->environment ?? 'sandbox';
        $this->spin_url = config("ipospay.{$environment}.spin_url");
        $url = $this->spin_url . "/Payment/" . $type;
        $requestDTO = SpinPaymentRequestDTO::fromArray([
            'user_id' => $user->id,
            'ref_id' => generateTransactionRefId(8),
            'transaction_type' => 'credit',
            'amount' => $request->amount,
            'customer_name' => $request->contact['name'] ?? '',
            'customer_email' => $request->contact['email'] ?? '',
            'tpn' => $request->tpn,
            'auth_key' => $request->authKey,
            'merchant_number' =>  null,
            'tip_amount' => null,
            'external_receipt' => '',
            'capture_signature' => false,
            'get_extended_data' => true,
            'spin_proxy_timeout' => null,
            'url' => $url??null,
        ]);
        $paymentResult = $this->paymentProcessor->processPayment($requestDTO);
        Log::info("spinTrigger", (array)$paymentResult);
        $response = $paymentResult->result;
        $generalResponse = $response->GeneralResponse ?? null;
        if (!$generalResponse) {
            $generalResponse = $paymentResult->GeneralResponse ?? null;
        }
        if ($generalResponse->StatusCode == "0000") {
            $message = $generalResponse->Message??'Approved';
            $requestData = $request->all();
            $requestData['IPosToken'] = $paymentResult['IPosToken']??null;
            $requestData['RRN'] = $paymentResult['RRN']??null;
            CreateTransactionAndSubscriptionJob::dispatch($requestData, $user, $ref_id, $url)->onQueue(config('app.job_queue'));
            return response()->json(['status' => true, 'message' => $message, 'charge_id' => $ref_id], 200);
        }
        return response()->json(['status' => false, 'message' => 'Something went wrong','result' =>  $paymentResult], 400);
    }
    public function paymentProdivderQueryUrl(Request $request)
    {
        Log::info('Payload=>', $request->all());
        $crm_subscription_id = $request->subscriptionId;
        $type = $request->type;
        if ($type == 'cancel_subscription') {
            $transaction = Transaction::where('crm_subscription_id', $crm_subscription_id)->first();
            if(!$transaction)
            {
                return json_encode(['failed' => true]);
            }
            $transaction->transaction_status = 'canceled';
            $transaction->save();
            if($transaction->subscription)
            {
                $subscription = $transaction->subscription;
                $subscription->status = 'canceled';
                $subscription->save();
            }
            return json_encode(['success' => true]);
            // $user_id = $transaction->user_id;
            // $apiKey = get_option($user_id, 'api_key');
           
            // try {
            //     $event = "subscription.updated";
            //     $subscriptionData = [
            //         'event' => $event,
            //         'ghlSubscriptionId' => $transaction->crm_subscription_id ?? null,
            //         "subscriptionSnapshot" => [
            //             "id" => $transaction->crm_subscription_id,
            //             "status" => $transaction->transaction_status,
            //         ],
            //         'locationId' => $transaction->location_id ?? null,
            //         'apiKey' => $apiKey,
            //     ];
            //     $subscriptionService = new SubscriptionService();
            //     $subscriptionService->triggerSubscriptionEvent($subscriptionData);
            // } catch (\Throwable $th) {
            //     Log::info('ErrorPostApi', $th->getMessage());
            // }
        }
        if($type == 'verify')
        {
            $found = Transaction::where("ref_id", $request->chargeId)->where('crm_transaction_id',$request->transactionId)->first();
            if(!$found)
            {
                return json_encode(['failed' => true]);
            }
            return json_encode(['success' =>true]);
        }
        return json_encode(['success' => false]);
    }
}
