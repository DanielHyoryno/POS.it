<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('inventory_movements', function (Blueprint $t) {
            $t->foreignId('lot_id')->nullable()->after('item_id')->constrained('item_lots')->nullOnDelete();
        });
    }
    public function down(): void {
        Schema::table('inventory_movements', function (Blueprint $t) {
            $t->dropConstrainedForeignId('lot_id');
        });
    }
};
