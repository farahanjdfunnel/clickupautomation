<?php

namespace App\Jobs;

use App\Helper\CRM;
use App\Models\CrmAuths;
use App\Models\User;
use App\Services\PaymentProviderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RegisterPaymentProvider implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $maxExceptions = 3;
    public $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    public function handle()
    {
        $user = $this->user;
        PaymentProviderService::configureIfNotPresent($user);
    }
}
