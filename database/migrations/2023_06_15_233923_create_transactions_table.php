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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('unit_id')->nullable()->constrained()->cascadeOnDelete();
            $table->integer('channel'); // 1 = m-pesa, 2 = card, 3 = invoice, 4 = paypal
            $table->string('trans_id')->nullable();
            $table->dateTime('trans_time')->nullable();
            $table->double('amount')->nullable();
            $table->string('business_short_code')->nullable();
            $table->string('account_no')->nullable();
            $table->string('third_party_trans_id')->nullable();
            $table->string('msisdn')->nullable();
            $table->string('email')->nullable();
            $table->string('name')->nullable();
            $table->ipAddress('ip')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
