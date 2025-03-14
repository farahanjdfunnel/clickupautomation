<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('crm_subscription_id')->nullable();
            $table->string('crm_order_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('currency')->nullable();
            $table->decimal('charge_amount', 8, 2)->nullable();
            $table->longText('card_token')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('next_billing_date')->nullable();
            $table->string('status')->nullable();
            $table->string('billing_details')->nullable();

            $table->timestamps();
            $table->index(['status', 'crm_subscription_id']);
            $table->index(['status', 'user_id', 'crm_subscription_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
};
