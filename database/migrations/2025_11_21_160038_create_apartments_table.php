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
            $table->string('nameOfOwner');
            $table->string('address');
            $table->string('city');
            $table->integer('numberOfRooms');
            $table->decimal('rentPrice', 8, 2);
            $table->boolean('isAvailable')->default(true);
            $table->string('imageUrl')->nullable();
            $table->string('description')->nullable();
            $table->integer('area');

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
