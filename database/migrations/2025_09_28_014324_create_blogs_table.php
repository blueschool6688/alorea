<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('posts_count')->default(0); // Số bài viết
            $table->timestamps();

            $table->index('slug');
            $table->index(['is_active', 'sort_order']);
        });


        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable(); // Mô tả ngắn
            $table->longText('content'); // Nội dung chính
            $table->string('featured_image')->nullable(); // Ảnh đại diện
            $table->json('gallery')->nullable(); // Gallery ảnh
            $table->string('meta_title')->nullable(); // SEO title
            $table->text('meta_description')->nullable(); // SEO description
            $table->json('meta_keywords')->nullable(); // SEO keywords
            $table->foreignId('category_id')->nullable()->constrained('blog_categories')->nullOnDelete();
            $table->string('author_name')->nullable(); // Tên tác giả
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->boolean('is_featured')->default(false); // Bài viết nổi bật
            $table->boolean('allow_comments')->default(true); // Cho phép bình luận
            $table->integer('views_count')->default(0); // Lượt xem
            $table->datetime('published_at')->nullable(); // Thời gian xuất bản
            $table->json('tags')->nullable(); // Tags
            $table->text('source_url')->nullable(); // Nguồn bài viết (nếu có)
            $table->integer('reading_time')->nullable(); // Thời gian đọc (phút)
            $table->json('social_shares')->nullable(); // Số lượt share mạng xã hội
            $table->timestamps();

            // Indexes
            $table->index(['status', 'published_at']);
            $table->index('category_id');
            $table->index('author_name');
            $table->index('is_featured');
            $table->index('slug');
            $table->fullText(['title', 'excerpt', 'content']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('blogs');
    }
};
