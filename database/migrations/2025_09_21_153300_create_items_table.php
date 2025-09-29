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
        Schema::create('items', function (Blueprint $t) {
            $t->id();
            $t->string('name')->unique();
            $t->enum('base_unit', ['g','ml','pcs']);
            $t->decimal('current_qty', 12, 3)->default(0);
            $t->decimal('low_stock_threshold', 12, 3)->default(0);
            $t->decimal('cost_price', 12, 2)->nullable(); // noted earlier
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
