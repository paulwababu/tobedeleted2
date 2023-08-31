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
        Schema::create('unknown_transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->integer('channel'); // 1 = m-pesa, 2 = card, 3 = invoice, 4 = paypal
            $table->string('trans_id')->nullable();
            $table->dateTime('trans_time')->nullable();
            $table->double('amount')->nullable();
            $table->string('account_no');
            $table->string('msisdn')->nullable();
            $table->string('email')->nullable();
            $table->string('name')->nullable();
            $table->boolean('status')->default(false); // 0 = pending, 1 = resolved
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unknown_transactions');
    }
};
