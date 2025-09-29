<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('item_lots', function (Blueprint $t) {
            $t->id();
            $t->foreignId('item_id')->constrained()->cascadeOnDelete();
            $t->decimal('qty', 12, 3);                 // remaining qty in this lot
            $t->date('expiry_date')->nullable();       // null = non-expiring
            $t->timestamp('received_at')->nullable();
            $t->decimal('cost_price', 12, 2)->nullable(); // per-unit override (optional)
            $t->string('note')->nullable();
            $t->timestamps();

            $t->index(['item_id','expiry_date']);
        });
    }
    public function down(): void { Schema::dropIfExists('item_lots'); }
};

