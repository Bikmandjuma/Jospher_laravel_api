<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->uuid('reference_id')->unique();
            $table->string('phone');
            $table->decimal('amount', 10, 2);
            $table->integer('duration');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('active_days');
            $table->string('status')->default('PENDING');
            $table->timestamps();

        });

    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
