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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('crm_invoice_id')->nullable();
            $table->string('crm_order_id')->nullable();
            $table->string('crm_transaction_id')->nullable();
            $table->string('crm_subscription_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('ref_id')->nullable();
            $table->string('transaction_status')->nullable();
            $table->string('currency')->nullable();
            $table->decimal('charge_amount', 8, 2)->nullable();
            $table->decimal('paid_amount', 8, 2)->nullable();
            $table->string('location_id')->nullable();
            $table->string('crm_contact_id')->nullable();
            $table->bigInteger('parent_transaction_id')->nullable();
            $table->string('ipospays_transaction_id')->nullable();
            $table->timestamps();

            // Index for `ref_id`
            $table->index('ref_id');

            // Composite index for `ref_id` and `location_id`
            $table->index(['ref_id', 'location_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
