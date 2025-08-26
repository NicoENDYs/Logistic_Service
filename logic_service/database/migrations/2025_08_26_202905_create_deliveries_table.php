<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')
                ->constrained('trips')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('customer_name', 150);
            $table->string('delivery_address');
            $table->timestamp('delivery_time')->nullable();
            $table->enum('status', ['pendiente', 'entregado', 'fallido'])->default('pendiente');
            $table->timestamps();

            $table->index('trip_id');
        });
    }

    public function down(): void {
        Schema::dropIfExists('deliveries');
    }
};
