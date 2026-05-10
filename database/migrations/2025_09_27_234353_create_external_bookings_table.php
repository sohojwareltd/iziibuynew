<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('external_bookings', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid');
            $table->string('booking_number')->nullable();
            $table->string('phone_number')->nullable();
            $table->foreignId('payment_method_access_id')->constrained('payment_method_accesses', 'id')->cascadeOnDelete();
            $table->integer('tax')->default(0);
            $table->integer('subtotal')->default(0);
            $table->integer('total')->default(0);
            $table->string('currency')->default('NOK');
            $table->string('payment_method')->default('elavon');
            $table->string('payment_id')->nullable();
            $table->string('payment_url')->nullable();
            $table->string('status')->default('PENDING');
            $table->string('payment_status')->default('PENDING');
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('external_bookings');
    }
};
