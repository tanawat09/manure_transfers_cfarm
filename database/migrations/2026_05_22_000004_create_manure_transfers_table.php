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
        Schema::create('manure_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_no')->unique();
            $table->foreignId('farm_id')->constrained('farms')->onDelete('restrict');
            $table->string('license_plate');
            $table->decimal('weight', 10, 2);
            $table->dateTime('out_datetime');
            $table->string('out_photo');
            $table->foreignId('out_user_id')->constrained('users')->onDelete('restrict');
            $table->dateTime('received_datetime')->nullable();
            $table->foreignId('pile_id')->nullable()->constrained('manure_piles')->onDelete('restrict');
            $table->string('receive_photo')->nullable();
            $table->foreignId('receive_user_id')->nullable()->constrained('users')->onDelete('restrict');
            $table->string('status')->default('pending'); // pending, received, cancelled
            $table->text('remark')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manure_transfers');
    }
};
