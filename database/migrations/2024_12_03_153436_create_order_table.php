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
        Schema::create('orders', function (Blueprint $table) {
            $table->id('id_order')->autoIncrement();
            $table->unsignedBigInteger('id_user');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('id_event');
            $table->foreign('id_event')->references('id_event')->on('events')->onDelete('cascade');
            $table->unsignedBigInteger('id_ticket');
            $table->foreign('id_ticket')->references('id_ticket')->on('tickets')->onDelete('cascade');
            $table->string('payment_proof');
            $table->enum('status', ['pending', 'approved', 'rejected']);
            $table->integer('quantity');
            $table->integer('total_price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
