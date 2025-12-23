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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->foreignId('entity_id')->constrained('entities')->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->foreignId('function_id')->constrained('contact_functions');
            $table->text('phone')->nullable();
            $table->text('mobile')->nullable();
            $table->text('email')->nullable();
            $table->boolean('gdpr_consent')->default(false);
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            
            $table->unique('number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
