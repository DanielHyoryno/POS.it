<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up(): void {
        Schema::create('categories', function (Blueprint $t) {
            $t->id();
            $t->string('name')->unique();
            $t->string('slug')->unique();
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->string('icon')->nullable();   // path or classname (optional)
            $t->string('color')->nullable();  // hex or tailwind key (optional)
            $t->timestamps();
        });

        Schema::table('products', function (Blueprint $t) {
            $t->foreignId('category_id')->nullable()->after('type')->constrained('categories')->nullOnDelete();
        });
    }
    public function down(): void {
        Schema::table('products', fn($t) => $t->dropConstrainedForeignId('category_id'));
        Schema::dropIfExists('categories');
    }
};
