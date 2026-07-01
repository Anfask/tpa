<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspector_id')->constrained('users')->onDelete('cascade');
            $table->string('type'); // 'teacher', 'admin', 'campus'
            
            // Explicit relations for simpler SQL querying and indexing
            $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('campus_id')->nullable()->constrained('campuses')->onDelete('cascade');
            $table->foreignId('class_id')->nullable()->constrained('classes')->onDelete('cascade');
            
            $table->decimal('score', 8, 2); // aggregate score percentage or points
            $table->json('raw_data'); // JSON structure storing: question_id -> score, comment
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
