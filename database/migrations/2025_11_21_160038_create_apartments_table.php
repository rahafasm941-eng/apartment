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
   
    Schema::create('apartments', function (Blueprint $table) {
        $table->id();
        $table->timestamps();

        // Basic info
        $table->string('address');
        $table->string('city');
        $table->string('neighborhood');
        $table->text('description')->nullable();
        $table->string('type')->default('economic');

        // Pricing
        $table->decimal('price_per_month', 8, 2);

        // Specifications
        $table->integer('area');
        $table->integer('number_of_rooms')->default(1);
        $table->integer('bathrooms')->default(1);
        $table->boolean('is_available')->default(true);
        $table->integer('rating')->default(0);

        // Media
        $table->string( 'apartment_image');
        $table->json( 'details_image');


        // Location
        $table->double('latitude', 10, 8);
        $table->double('longitude', 11, 8);

        // Extra features
        $table->json('features')->nullable();

        // Owner
        $table->foreignId('user_id')
              ->constrained('users')
              ->onDelete('cascade');

        // Admin approval
        $table->boolean('is_approved')->default(false);
    });
}

    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apartments');
    }
};
