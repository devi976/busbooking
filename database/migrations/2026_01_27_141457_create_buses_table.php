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
        Schema::create('buses', function (Blueprint $table) {
            $table->id();
            $table->string('bus_name');
            $table->string('from');
            $table->string('to');
            $table->text('stops')->nullable(); 
            $table->date('travel_date');
            $table->time('departure_time');
            $table->time('arrival_time')->nullable();
            $table->integer('total_seats');
            $table->integer('available_seats');
            $table->decimal('fare', 8, 2);
            $table->foreignId('operator_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buses');
    }
};
