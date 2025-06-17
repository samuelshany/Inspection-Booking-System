<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_availabilities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->tinyInteger('day_of_week'); 
            $table->date('booking_date')->nullable(); 
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['team_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_availabilities');
    }
};
