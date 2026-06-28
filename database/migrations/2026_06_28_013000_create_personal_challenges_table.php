<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personal_challenges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('duration_days')->default(7);
            $table->string('status')->default('active'); // active, paused, completed, failed
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('challenge_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_challenge_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->boolean('is_done')->default(true);
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['personal_challenge_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenge_progress');
        Schema::dropIfExists('personal_challenges');
    }
};
