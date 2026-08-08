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
        Schema::create('otp_password_resets', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->string('otp_code'); 
            $table->string('reset_token')->nullable()->unique();
            $table->timestamp('otp_expires_at');
            $table->timestamp('token_expires_at')->nullable();
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_password_resets');
    }
};
