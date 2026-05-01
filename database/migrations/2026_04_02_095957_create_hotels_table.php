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
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->nullable();
            $table->string('address', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('image', 255)->nullable();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->decimal('rating', 2, 1)->default(0.0);
            $table->string('review_text', 50)->nullable();
            $table->integer('review_count')->default(0);
            $table->integer('price')->nullable();
            $table->integer('old_price')->nullable();
            $table->string('checkin_time', 10)->default('14:00');
            $table->string('checkout_time', 10)->default('12:00');
            $table->boolean('is_active')->default(false);
            $table->boolean('is_weekend_deal')->default(false);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
