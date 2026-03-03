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
    Schema::create('bus_stop_timings', function (Blueprint $table) {
        $table->id();
        $table->foreignId('bus_id')->constrained()->onDelete('cascade');
        $table->string('stop_name');
        $table->time('arrival_time');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bus_stop_timings');
    }
};
