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
            $table->id();
            $table->string('names')->nullable();
            $table->string('provider_name')->nullable();
            $table->string('provider_id')->nullable()->unique();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('gender')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable()->unique(); 
            $table->string('dob')->nullable();
            $table->string('image')->nullable();
            $table->string('password')->nullable();
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }

}
