<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('remarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspector_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->text('content');
            $table->boolean('is_private')->default(false); // true for private internal notes, false for remarks visible to teachers
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('remarks');
    }
};
