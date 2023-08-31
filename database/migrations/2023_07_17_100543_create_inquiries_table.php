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
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('msisdn');
            $table->string('email');
            $table->text('inquiry');
            $table->text('response')->nullable();
            $table->dateTime('response_date')->nullable();
            $table->foreignId('response_by')->nullable()->references('id')->on('users')->cascadeOnDelete();
            $table->boolean('status')->default(false); // 0 = Pending, 1 = Resolved
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
