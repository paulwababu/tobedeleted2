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
        Schema::create('onboardings', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->integer('user_type'); // 1 = Landlord/owner, 2 = Agent, 3 = Tenant
            $table->string('name');
            $table->string('msisdn')->unique();
            $table->string('email')->unique();
            $table->string('location')->nullable();
            $table->string('national_id_no')->nullable(); // could be ID or Passport No
            $table->boolean('status')->default(false); // 0 = Pending, 1 = Approved
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onboardings');
    }
};
