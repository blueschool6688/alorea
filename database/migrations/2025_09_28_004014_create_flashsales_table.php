<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('flash_sales', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('banner_image')->nullable();
            $table->datetime('start_time');
            $table->datetime('end_time')->nullable();
            $table->decimal('discount_percentage', 5, 2)->default(0); // Giảm giá %
            $table->decimal('max_discount_amount', 15, 2)->nullable(); // Số tiền giảm tối đa
            $table->integer('used_quantity')->default(0); // Số lượng đã sử dụng
            $table->enum('status', ['draft', 'active', 'paused', 'ended'])->default('draft');
            $table->timestamps();
            $table->index(['status', 'start_time', 'end_time']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('flash_sales');
    }
};
