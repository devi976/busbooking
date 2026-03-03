<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('seat_locks', function (Blueprint $table) {
        $table->id();
        $table->foreignId('bus_id')->constrained()->onDelete('cascade');
        $table->string('seat_number');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->timestamp('expires_at');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seat_locks');
    }
};
