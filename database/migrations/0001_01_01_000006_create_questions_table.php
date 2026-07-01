<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_criteria_id')->constrained('sub_criteria')->onDelete('cascade');
            $table->text('question_text');
            $table->integer('max_score')->default(10);
            $table->integer('order_index')->default(0); // Question ordering
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
