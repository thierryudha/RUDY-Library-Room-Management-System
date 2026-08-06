<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 30)->nullable()->after('email');
            $table->string('profile_photo_path')->nullable()->after('phone');
            $table->foreignId('role_id')->nullable()->after('profile_photo_path');
            $table->string('user_status', 30)->default('active')->after('role_id');
            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('role_id')->references('id')->on('roles')
                ->onUpdate('no action')->onDelete('no action');
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
