<?php

namespace App\Jobs;

use App\Helper\CRM;
use App\Models\CrmAuths;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateUserAccount implements ShouldQueue
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
        try {
            $user = $this->user;
            User::updateLocationInfo($user);
        } catch (\Throwable $th) {
            Log::info('error location response ', [$th->getMessage()]);
        }
    }
}
