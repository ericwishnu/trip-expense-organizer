<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trip_exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->cascadeOnDelete();
            $table->string('currency', 3);
            $table->decimal('rate', 12, 6);
            $table->timestamps();

            $table->unique(['trip_id', 'currency']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_exchange_rates');
    }
};
