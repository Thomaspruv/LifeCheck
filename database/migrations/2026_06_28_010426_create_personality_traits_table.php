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
        Schema::create('personality_traits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('openness')->nullable()->comment('Ouverture (0-100)');
            $table->unsignedTinyInteger('conscientiousness')->nullable()->comment('Conscienciosité (0-100)');
            $table->unsignedTinyInteger('extraversion')->nullable()->comment('Extraversion (0-100)');
            $table->unsignedTinyInteger('agreeableness')->nullable()->comment('Agréabilité (0-100)');
            $table->unsignedTinyInteger('neuroticism')->nullable()->comment('Névrosisme (0-100)');
            $table->json('answers')->nullable()->comment('Réponses brutes au questionnaire');
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personality_traits');
    }
};
