<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments')
                ->onUpdate('no action')->onDelete('no action');
            $table->string('name', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_programs');
    }
};
