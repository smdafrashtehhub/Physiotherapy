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
        Schema::create('working_hours', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('start_hour')->nullable();
            $table->time('end_hour')->nullable();
            $table->unique(['date','start_hour','end_hour']);
            $table->unique(['date','start_hour']);
            $table->unique(['date','end_hour']);
            $table->enum('closed',['yes','no']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('working_hours');
    }
};
