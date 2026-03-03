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
		
		Schema::create('products', function (Blueprint $table) {
			$table->id(); $table->string('name'); $table->string('sku')->unique();
			$table->integer('stock')->default(0); $table->decimal('price', 8, 2);
			$table->timestamps();
		});

		Schema::create('product_audits', function (Blueprint $table) {
			$table->id(); $table->foreignId('product_id')->constrained()->cascadeOnDelete();
			$table->integer('before_stock'); $table->integer('after_stock');
			$table->string('action'); $table->string('user_id')->nullable();
			$table->timestamps();
		});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products_audits');
    }
};
