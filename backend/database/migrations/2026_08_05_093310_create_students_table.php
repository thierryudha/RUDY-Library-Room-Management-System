<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')
                ->onUpdate('no action')->onDelete('cascade');
            $table->string('student_id_number', 20)->unique();
            $table->foreignId('study_program_id')->constrained('study_programs')
                ->onUpdate('no action')->onDelete('no action');
            $table->integer('class_of');
            $table->string('activation_proof_path');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
