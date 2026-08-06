<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_members', function (Blueprint $table) {
            $table->foreignId('booking_id')->constrained('bookings')
                ->onUpdate('no action')->onDelete('no action');
            $table->foreignId('user_id')->constrained('users')
                ->onUpdate('no action')->onDelete('no action');
            $table->timestamp('created_at');
            $table->primary(['booking_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_members');
    }
};
