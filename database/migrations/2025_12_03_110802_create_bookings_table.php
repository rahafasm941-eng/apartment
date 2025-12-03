<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('bookings', function (Blueprint $table) {
        $table->id();
        $table->timestamps();

        // Relations
        $table->foreignId('user_id')
              ->constrained()
              ->onDelete('cascade');

        $table->foreignId('apartment_id')
              ->constrained()
              ->onDelete('cascade');

        // Booking period
        $table->date('start_date');
        $table->date('end_date');

        // Price
        $table->decimal('total_price', 10, 2);

        // Status (important!)
        $table->enum('status', [
            'pending',   // بانتظار موافقة صاحب الشقة
            'approved',  // تمت الموافقة
            'rejected',  // تم الرفض
            'canceled',  // ألغاها المستأجر
        ])->default('pending');

        // Optional
        $table->text('notes')->nullable();
    });
}

public function down(): void
{
    Schema::dropIfExists('bookings');
}
};