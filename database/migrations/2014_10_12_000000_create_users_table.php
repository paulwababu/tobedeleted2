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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('msisdn')->unique();
            $table->timestamp('msisdn_verified_at')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('location')->nullable();
            $table->integer('status')->default(0); // 0 = Pending, 1 = Approved, 2 = Suspended
            $table->string('national_id_no')->nullable()->unique(); // could be ID or Passport No
            $table->string('profile_photo_url')->nullable();
            $table->integer('gender')->default(3); // 1 = Male, 2 = Female, 3 = Anonymous
            $table->string('password');
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
