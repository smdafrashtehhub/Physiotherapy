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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('family')->nullable();
            $table->string('user_name')->nullable();
            $table->integer('national_code')->nullable();
            $table->string('address')->nullable();
            $table->integer('age')->nullable();
            $table->enum('gender',['male','female'])->nullable();
            $table->date('birthday')->nullable();
            $table->enum('status',['active','inactive']);
            $table->text('disease_record')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('phone_number');
            $table->string('phone_number_verify_code');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
