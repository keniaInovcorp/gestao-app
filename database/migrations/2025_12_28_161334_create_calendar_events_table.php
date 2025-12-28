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
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('time');
            $table->integer('duration')->default(60);
            $table->boolean('share')->default(false);
            $table->boolean('knowledge')->default(false);
            $table->foreignId('entity_id')->nullable()->constrained('entities')->onDelete('set null');
            $table->foreignId('type_id')->nullable()->constrained('calendar_types')->onDelete('set null');
            $table->foreignId('action_id')->nullable()->constrained('calendar_actions')->onDelete('set null');
            $table->text('description')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};
