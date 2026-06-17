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
        Schema::table('products', function (Blueprint $table) {
            // Technical specifications columns
            $table->string('concentration')->nullable()->comment('Nồng độ tinh dầu');
            $table->integer('volume_ml')->nullable()->comment('Dung tích (ml)');
            $table->string('origin')->nullable()->comment('Xuất xứ');
            $table->string('longevity')->nullable()->comment('Độ lưu hương');
            $table->string('sillage')->nullable()->comment('Độ tỏa hương');
            $table->json('usage_time')->nullable()->comment('Thời điểm sử dụng');
            $table->text('main_ingredients')->nullable()->comment('Thành phần chính');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop technical specifications columns
          
        });
    }
};
