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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->integer('type'); // 1 = Water, 2 = Electricity
            $table->string('bill_month'); // E.g. "July 2023"
            $table->double('units_consumed');
            $table->double('cost_per_unit');
            $table->double('amount_payable');
            $table->double('amount_paid');
            $table->double('balance')->nullable();
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
        Schema::dropIfExists('bills');
    }
};
