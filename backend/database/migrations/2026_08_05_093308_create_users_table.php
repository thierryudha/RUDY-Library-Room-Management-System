<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 100)->unique();
            $table->string('password', 255);
            $table->string('phone', 30)->nullable();
            $table->string('profile_photo_path')->nullable();
            $table->foreignId('role_id')->constrained('roles')->onUpdate('no action')->onDelete('no action');
            $table->string('user_status', 30);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn(['phone', 'profile_photo_path', 'role_id', 'user_status', 'deleted_at']);
        });
    }
};
