<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('flash_sale_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flash_sale_id')->constrained('flash_sales')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->decimal('original_price', 15, 2);
            $table->decimal('sale_price', 15, 2);
            $table->decimal('discount_percentage', 5, 2);
            $table->integer('quantity_limit')->nullable();
            $table->integer('sold_quantity')->default(0);
            $table->timestamps();

            $table->unique(['flash_sale_id', 'product_id']);
            $table->index('flash_sale_id');
            $table->index('product_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('flash_sale_products');
    }
};
