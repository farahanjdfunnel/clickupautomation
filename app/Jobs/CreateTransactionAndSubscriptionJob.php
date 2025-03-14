<?php

namespace App\Jobs;

use App\Helper\CRM;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Transaction;
use App\Models\Subscription;
use App\Models\User;
use App\Services\SubscriptionDateCalculator;
use Illuminate\Support\Facades\Log;

class CreateTransactionAndSubscriptionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $request;
    protected $user;
    protected $refId;
    protected $url;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $requestData, User $user, $refId, $url)
    {
        $this->request = $requestData;
        $this->user = $user;
        $this->refId = $refId;
        $this->url = $url;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Transaction creation
        $transaction = new Transaction();
        $transaction->crm_transaction_id = $this->request['transactionId'] ?? null;
        $transaction->crm_subscription_id = $this->request['subscriptionId'] ?? null;
        $transaction->crm_invoice_id = $this->request['invoiceId'] ?? null;
        $transaction->crm_order_id = $this->request['orderId'] ?? null;
        $transaction->ref_id = $this->refId;
        $transaction->transaction_status = 'pending';
        $transaction->charge_amount = $this->request['amount'] ?? 0.00;
        $transaction->currency = $this->request['currency'] ?? null;
        $transaction->location_id = $this->request['locationId'] ?? null;
        $transaction->crm_contact_id = $this->request['contact']['id'] ?? null;
        $transaction->user_id = $this->user->id;
        $transaction->ipospays_transaction_id = $this->request['RRN']??null;
        $transaction->save();

        // Subscription creation
        if ($this->request['subscriptionId'] ?? null) {
            $priceConfig = [];
            $isTaxesEnabled = false;
            if ($this->request['invoiceId']) {
                $company_id = $this->user->id;
                $invoice_data = CRM::crmV2($company_id, 'invoices/' . $this->request['invoiceId'] . '?altType=location&altId=' . $this->request['locationId'], 'get');
                $invoice_response = is_string($invoice_data) ? json_decode($invoice_data,true) : (array)$invoice_data;
                Log::info("invoice_response", (array)$invoice_response);
                if (!empty($invoice_response['invoiceItems'])) {
                    $priceConfig = [];
                    $isTaxesEnabled = $invoice_response['automaticTaxesCalculated'] ?? false;
                    foreach ($invoice_response['invoiceItems'] as $item) {
                        $priceConfig[] = [
                            'amount' => $item->amount,
                            'currency' => $item->currency,
                            'taxes' => $item->taxes ?? [],
                            'recurring' =>  null,
                            'trialPeriod' =>  0,
                            'totalCycles' => 0
                        ];
                    }
                }
            } 
            elseif ($this->request['orderId'])
            {
                $company_id = $this->user->id;
                $order_data = CRM::crmV2($company_id, 'payments/orders/' . $this->request['orderId']. '?altType=location&altId='. $this->request['locationId'], 'get');
                $order_data = is_string($order_data) ? json_decode($order_data,true) : (array)$order_data;
                Log::info("order Response",(array)$order_data);
                if (!empty($order_data['items'])) {
                    $priceConfig = [];
                    $isTaxesEnabled = $order_data['taxInclusive'] ?? false;
                    foreach ($order_data['items'] as $item) {
                        $priceConfig[] = [
                            'amount' => $item->price->amount,
                            'currency' => $item->price->currency,
                            'taxes' => $item->product->taxes ?? [],
                            'recurring' => $item->price->recurring??null,
                            'trialPeriod' => $item->price->trialPeriod??0,
                            'totalCycles' => $item->price->totalCycles??0
                        ];
                    }
                }
            }
            $totalAmount = 0;
            $totalTaxAmount = 0;
            // Calculate total amount and taxes
            foreach ($priceConfig as $config) {
                $itemAmount = $config['amount'] ?? 0;
                // Calculate tax for each item if taxes are enabled
                if ($isTaxesEnabled && !empty($config['taxes'])) {
                    foreach ($config['taxes'] as $tax) {
                        $rate = $tax['rate'] ?? 0;
                        $taxCalculation = $tax['calculation'] ?? 'exclusive';
                        if ($taxCalculation === 'exclusive') {
                            $totalTaxAmount += ($itemAmount * $rate) / 100;
                        }
                    }
                }
                $totalAmount += $itemAmount;
            }
            $finalAmount = $totalAmount + $totalTaxAmount;
            $subscriptionDetails = null;
            if(!empty($priceConfig))
            {
                $dates = app(SubscriptionDateCalculator::class)->calculateDates($priceConfig[0]);
                $subscriptionDetails = [
                    'price_id' => $priceConfig[0]['_id'] ?? null,
                    'amount' => $totalAmount,
                    'currency' => $priceConfig[0]['currency'] ?? $this->request['currency'],
                    'trial_ends_at' => $dates['trial_ends_at']??null,
                    'next_charge_date' => $dates['next_charge_date'],
                    'billing_details' => [
                        'interval' => $dates['billing_interval'],
                        'interval_count' => $dates['billing_interval_count'],
                        'total_cycles' => $dates['total_cycles'],
                    ],
                ];
            }
            
            $subscription = new Subscription();
            $subscription->crm_subscription_id = $this->request['subscriptionId'] ?? null;
            $subscription->crm_order_id = $this->request['orderId'] ?? null;
            $subscription->trial_ends_at = $subscriptionDetails['trial_ends_at'] ?? null;
            $subscription->next_billing_date = $subscriptionDetails['next_charge_date'] ??'';
            $subscription->status = 'pending';
            $subscription->user_id = $this->user->id??null;
            $subscription->currency = $this->request['currency'] ?? null;
            $subscription->charge_amount = $finalAmount ?? null;
            $subscription->billing_details = (isset($subscriptionDetails)) ? json_encode($subscriptionDetails['billing_details']) : json_encode([]);
            $subscription->card_token = $this->request['IPosToken']??null;
            $subscription->save();
        }
    }
}
