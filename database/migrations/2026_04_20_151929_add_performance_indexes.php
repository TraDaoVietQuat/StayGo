<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── hotels ──────────────────────────────────────────────────────
        // Query phổ biến nhất: WHERE is_active=1 ORDER BY rating DESC
        Schema::table('hotels', function (Blueprint $table) {
            $table->index(['is_active', 'rating'],         'idx_hotels_active_rating');
            $table->index(['is_active', 'type'],           'idx_hotels_active_type');
            $table->index(['is_active', 'location_id'],    'idx_hotels_active_location');
            $table->index(['is_active', 'is_weekend_deal'],'idx_hotels_active_deal');
            $table->index('price',                          'idx_hotels_price');
        });

        // ── bookings ─────────────────────────────────────────────────────
        // Query kiểm tra phòng trống: WHERE room_id=? AND status IN(...) AND check_in < ? AND check_out > ?
        Schema::table('bookings', function (Blueprint $table) {
            $table->index(['room_id', 'status', 'check_in', 'check_out'], 'idx_bookings_availability');
            $table->index(['user_id', 'status'],  'idx_bookings_user_status');
            $table->index('status',               'idx_bookings_status');
            $table->index('created_at',           'idx_bookings_created');
        });

        // ── rooms ────────────────────────────────────────────────────────
        Schema::table('rooms', function (Blueprint $table) {
            $table->index(['hotel_id', 'price'], 'idx_rooms_hotel_price');
        });

        // ── reviews ──────────────────────────────────────────────────────
        Schema::table('reviews', function (Blueprint $table) {
            $table->index(['is_active', 'rating'],   'idx_reviews_active_rating');
            $table->index(['hotel_id', 'is_active'], 'idx_reviews_hotel_active');
        });

        // ── blog_posts ───────────────────────────────────────────────────
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->index(['is_active', 'created_at'], 'idx_blog_active_date');
        });

        // ── payments ─────────────────────────────────────────────────────
        Schema::table('payments', function (Blueprint $table) {
            $table->index('payment_status', 'idx_payments_status');
            $table->index('created_at',     'idx_payments_created');
        });
    }

    public function down(): void
    {
        Schema::table('hotels',     function (Blueprint $t) {
            $t->dropIndex('idx_hotels_active_rating');
            $t->dropIndex('idx_hotels_active_type');
            $t->dropIndex('idx_hotels_active_location');
            $t->dropIndex('idx_hotels_active_deal');
            $t->dropIndex('idx_hotels_price');
        });
        Schema::table('bookings',   function (Blueprint $t) {
            $t->dropIndex('idx_bookings_availability');
            $t->dropIndex('idx_bookings_user_status');
            $t->dropIndex('idx_bookings_status');
            $t->dropIndex('idx_bookings_created');
        });
        Schema::table('rooms',      fn($t) => $t->dropIndex('idx_rooms_hotel_price'));
        Schema::table('reviews',    function (Blueprint $t) {
            $t->dropIndex('idx_reviews_active_rating');
            $t->dropIndex('idx_reviews_hotel_active');
        });
        Schema::table('blog_posts', fn($t) => $t->dropIndex('idx_blog_active_date'));
        Schema::table('payments', function (Blueprint $t) {
            $t->dropIndex('idx_payments_status');
            $t->dropIndex('idx_payments_created');
        });
    }
};
