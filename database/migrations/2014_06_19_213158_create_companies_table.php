<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('name');
            $table->string('msisdn');
            $table->string('email');
            $table->string('location')->nullable();
            $table->double('wallet_balance')->default(0);
            $table->string('wallet_account_no')->unique();
            $table->integer('sms_channel')->default(1); // 1 = system mobilesasa settings, 2 = custom mobilesasa settings
            $table->string('sms_bearer_token')->nullable();
            $table->string('sms_sender_id')->default('MOBILESASA');
            $table->double('sms_units_balance')->default(0); // updated by calling mobilesasa api
            $table->integer('status')->default(0); // 0 = Pending, 1 = Approved, 2 = Suspended
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
