<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('score_summaries', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type', 50); // 'teacher', 'admin', 'campus'
            
            $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('campus_id')->nullable()->constrained('campuses')->onDelete('cascade');
            
            $table->string('period_type', 20); // 'weekly', 'monthly'
            $table->string('period_key', 20); // e.g. '2026-W24' or '2026-06'
            $table->decimal('average_score', 8, 2);
            $table->integer('inspection_count')->default(1);
            $table->timestamps();
            
            $table->unique(['entity_type', 'teacher_id', 'admin_id', 'campus_id', 'period_type', 'period_key'], 'score_summary_unique_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('score_summaries');
    }
};
