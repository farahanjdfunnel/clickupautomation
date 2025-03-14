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
        Schema::table('users', function (Blueprint $table) {
            $table->string('location_id')->nullable();
            $table->string('company_id')->nullable();
            $table->string('ghl_api_key')->nullable();
            $table->integer('role')->default(2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('location_id');
            $table->dropColumn('ghl_api_key');
            $table->dropColumn('role');
        });
    }
};
