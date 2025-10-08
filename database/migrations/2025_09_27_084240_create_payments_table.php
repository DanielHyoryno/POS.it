<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $t->string('method');
            $t->decimal('amount', 12, 2);
            $t->string('notes')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('payments');
    }
};
