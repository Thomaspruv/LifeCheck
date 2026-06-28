<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('breathing_exercises', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('benefits')->nullable();
            $table->text('description')->nullable();
            $table->string('type')->default('breathing'); // breathing, meditation
            $table->string('category')->nullable();
            $table->json('pattern_data')->nullable(); // {inhale, hold1, exhale, hold2} in seconds
            $table->json('duration_options')->nullable(); // [1, 3, 5, 10] minutes
            $table->string('icon')->default('🧘');
            $table->string('color')->default('#6366f1');
            $table->text('instructions')->nullable();
            $table->boolean('is_default')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('meditation_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exercise_id')->nullable()->constrained('breathing_exercises')->nullOnDelete();
            $table->string('exercise_name');
            $table->string('type'); // breathing, meditation
            $table->integer('duration_seconds');
            $table->boolean('completed')->default(true);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meditation_sessions');
        Schema::dropIfExists('breathing_exercises');
    }
};
