<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('check_ins', function (Blueprint $table) {
            $table->decimal('sentiment_score', 4, 2)->nullable()->after('notes');
            $table->string('sentiment_label', 20)->nullable()->after('sentiment_score');
            $table->string('sentiment_intensity', 20)->nullable()->after('sentiment_label');
        });
    }

    public function down(): void
    {
        Schema::table('check_ins', function (Blueprint $table) {
            $table->dropColumn(['sentiment_score', 'sentiment_label', 'sentiment_intensity']);
        });
    }
};
