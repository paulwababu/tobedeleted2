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
        Schema::create('rental_payments', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('msisdn')->nullable();
            $table->string('email')->nullable();
            $table->string('rental_month'); // E.g. "July 2023"
            $table->double('amount_payable');
            $table->double('amount_paid');
            $table->double('balance');
            $table->string('description'); // E.g. deposit, rent, deposit & rent
            $table->boolean('payment_status')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_payments');
    }
};
