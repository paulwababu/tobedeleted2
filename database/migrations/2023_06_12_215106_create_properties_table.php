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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->integer('no_of_units');
            $table->integer('type'); // 1 = Residential, 2 = Commercial
            $table->text('description');
            $table->text('features')->nullable();
            $table->foreignId('county_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('location');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('kyc_docs_url')->nullable();
            $table->text('google_map_embed_code')->nullable();
            $table->string('photo_url');
            $table->text('youtube_embed_code')->nullable();
            $table->boolean('status')->default(false); // 0 = Pending, 1 = Approved
            $table->boolean('is_featured')->default(false); // 0 = Yes, 1 = No
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
