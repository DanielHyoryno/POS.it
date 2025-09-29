<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('inventory_movements', function (Blueprint $t) {
            $t->string('reason', 32)->nullable(false)->change(); // room for 'sale','restock','adjust'
        });
    }
    public function down(): void {
        Schema::table('inventory_movements', function (Blueprint $t) {
            // put back your old type if you want (guessing)
            // $t->string('reason', 1)->change();
        });
    }
};