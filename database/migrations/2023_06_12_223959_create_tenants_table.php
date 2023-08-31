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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('msisdn');
            $table->string('email');
            $table->integer('no_of_occupants');
            $table->dateTime('check_in')->default(now()->toDateTimeString()); // Check in date
            $table->text('state_on_check_in'); // "Unit is brand new"
            $table->boolean('check_in_confirmation')->default(false); // 0 = pending, 1 = approved
            $table->dateTime('check_out')->nullable(); // Check out date
            $table->text('state_on_check_out')->nullable(); // "Unit needs repainting"
            $table->integer('check_out_status')->default(1); // 1 = checked in, 2 = check out requested, 3 = check out approved, 4 = checked out
            $table->integer('status')->default(1); // 1 = checked in, 2 = checked out
            $table->string('tenant_agreement_doc_url');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
