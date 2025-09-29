<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('product_bom_lines', function (Blueprint $t) {
            $t->id();
            $t->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $t->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $t->decimal('qty', 12, 3); // base unit of item
            $t->timestamps();

            $t->unique(['product_id','item_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('product_bom_lines');
    }
};

