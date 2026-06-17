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
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('short_description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('sku')->unique();
            $table->string('barcode')->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('stock')->default(0);
            $table->string('image')->nullable();
            $table->json('gallery')->nullable(); // Array of image paths
            $table->enum('status', ['active', 'inactive', 'draft'])->default('active');
            $table->enum('type', ['men', 'women', 'unisex'])->default('men');
            $table->boolean('is_featured')->default(false);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('sales_count')->default(0);
            $table->decimal('rating', 2, 1)->default(0);
            $table->integer('review_count')->default(0);
            $table->json('attributes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['status', 'is_featured']);
            $table->index(['category_id', 'status']);
            $table->index('price');
            $table->index('stock');
            $table->fullText(['name', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
