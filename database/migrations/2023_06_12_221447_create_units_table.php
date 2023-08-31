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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unit_type_id')->constrained()->cascadeOnDelete();
            $table->string('account_no')->unique(); // To be used for rental payments
            $table->string('label'); // e.g. A01, A02, A03
            $table->double('deposit'); // KES
            $table->double('rent'); // KES
            $table->boolean('is_water_postpaid')->default(false);
            $table->boolean('is_electricity_postpaid')->default(false); // KES
            $table->text('features'); // e.g. Balcony, Hot Shower
            $table->boolean('status')->default(false); // 0 = vacant, 1 = occupied
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
