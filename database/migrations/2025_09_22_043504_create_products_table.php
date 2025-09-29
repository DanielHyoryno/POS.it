<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('products', function (Blueprint $t) {
            $t->id();
            $t->string('name')->unique();
            $t->string('sku')->nullable()->unique();
            $t->enum('type', ['simple','composite'])->default('simple');
            $t->decimal('selling_price', 12, 2)->default(0);
            $t->boolean('is_active')->default(true);

            // simple product linkage
            $t->foreignId('linked_item_id')->nullable()->constrained('items')->nullOnDelete();
            $t->decimal('per_sale_qty', 12, 3)->nullable(); // base unit of linked item

            $t->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('products');
    }
};
