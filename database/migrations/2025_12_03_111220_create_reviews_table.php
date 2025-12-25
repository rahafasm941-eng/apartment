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
    Schema::create('reviews', function (Blueprint $table) {
        $table->id();
        $table->timestamps();

        // Relations
        $table->foreignId('user_id')
              ->constrained()
              ->onDelete('cascade');

        $table->foreignId('apartment_id')
              ->constrained()
              ->onDelete('cascade');

        // $table->foreignId('booking_id')
        //       ->constrained()
        //       ->onDelete('cascade');

        // Review Data
        $table->enum('rating', ['1', '2', '3', '4', '5']);

        $table->text('comment')->nullable();
    });
}

public function down(): void
{
    Schema::dropIfExists('reviews');
}

};
