<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('logo_name')->nullable();
            $table->string('address')->nullable();
            $table->string('hotline')->nullable();
            $table->string('email_contact')->nullable();


            $table->string('link_social_facebook')->nullable();
            $table->string('link_social_tiktok')->nullable();
            $table->string('link_social_youtube')->nullable();
            $table->string('link_social_instagram')->nullable();

            $table->string('banner_is_image')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('banner_video')->nullable();
            $table->string('title_banner')->nullable();
            $table->string('sub_title_banner')->nullable();


            $table->text('video_tiktok_review')->nullable();


            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
};
