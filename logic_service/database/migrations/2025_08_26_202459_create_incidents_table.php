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
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')
                ->constrained('trips')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('description');
            $table->enum('type', ['accidente', 'retraso', 'mecanico', 'otro'])->default('otro');
            $table->timestamp('reported_at')->useCurrent();
            $table->boolean('resolved')->default(false);
            $table->timestamps();

            $table->index('trip_id');
        });
    }

    public function down(): void {
        Schema::dropIfExists('incidents');
    }

};
