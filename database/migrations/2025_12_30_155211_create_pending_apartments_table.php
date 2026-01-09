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
        Schema::create('pending_apartments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            
        // Basic info
        $table->integer('apartment_id')->nullable();
        $table->string('address')->default('economic');
        $table->string('city')->default('damascus');
        $table->string('neighborhood')->default('al-midan');
        $table->text('description')->nullable();
        $table->string('type')->default('economic');

        // Pricing
        $table->decimal('price_per_month', 8, 2)->default(0);

        // Specifications
        $table->integer('area')->default(0) ;
        $table->integer('number_of_rooms')->default(1);
        $table->integer('bathrooms')->default(1);
        $table->boolean('is_available')->default(true);
        $table->integer('rating')->default(0);

        // Media
        $table->string( 'apartment_image')->default('');
        $table->json( 'details_image')->default('[]');


        // Location
        $table->double('latitude', 10, 8)->default(0);
        $table->double('longitude', 11, 8)->default(0);

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
        Schema::dropIfExists('pending_apartments');
    }
};
