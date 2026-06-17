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
        Schema::create('scents', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên mùi hương
            $table->string('slug')->unique(); // Đường dẫn thân thiện
            $table->text('description')->nullable(); // Mô tả
            $table->enum('type', ['top', 'middle', 'base']); // Loại note: top/middle/base
            $table->string('category')->nullable(); // Phân loại: floral, woody, fresh, oriental, etc.
            $table->string('color_hex', 7)->default('#000000'); // Màu đại diện
            $table->boolean('is_popular')->default(false); // Mùi hương phổ biến
            $table->integer('intensity')->default(5); // Cường độ mùi (1-10)
            $table->text('notes')->nullable(); // Ghi chú thêm
            $table->boolean('is_active')->default(true); // Trạng thái hoạt động
            $table->timestamps();

            // Indexes
            $table->index(['type', 'is_active']);
            $table->index(['category', 'is_active']);
            $table->index('is_popular');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scents');
    }
};
