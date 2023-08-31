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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('msisdn');
            $table->text('message');
            $table->text('api_response_message')->nullable();
            $table->string('tracking_id')->nullable();
            $table->integer('status')->default(0); // 0 = Failed, 1 = Pending, 2 = Delivered
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
