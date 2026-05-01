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
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title', 500);
            $table->string('category', 100);
            $table->text('summary');
            $table->longText('content');
            $table->string('thumb', 500);
            $table->string('img', 500);
            $table->string('author', 200)->default('Admin');
            $table->string('tags', 500)->nullable()->default('');
            $table->string('read_time', 50)->default('5 phút đọc');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
