<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            // Primary ID for users table
            $table->id();

            // Foreign key referencing the socialite_users table
            // $table->unsignedBigInteger('socialite_user_id');
            // $table->foreign('socialite_user_id')->references('id')->on('socialite_users')->onDelete('cascade');

            // User fields
            $table->string('names');
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable()->unique();
            $table->string('gender')->nullable();
            $table->date('dob')->nullable(); // Changed to 'date' for better handling of dates
            $table->string('image')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();

            // Timestamps for creation and update tracking
            $table->timestamps();

            // Index for faster lookups on socialite_user_id
            // $table->index('socialite_user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }

}
