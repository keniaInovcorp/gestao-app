<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('entities', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('number')->unique();
            $table->string('tax_number')->unique();
            $table->string('name');
            $table->text('address');
            $table->string('postal_code', 8);
            $table->string('city');
            $table->foreignId('country_id')->constrained('countries');
            $table->text('phone')->nullable();
            $table->text('mobile')->nullable();
            $table->string('website')->nullable();
            $table->text('email')->nullable();
            $table->boolean('gdpr_consent')->default(false);
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('entities');
    }
};
