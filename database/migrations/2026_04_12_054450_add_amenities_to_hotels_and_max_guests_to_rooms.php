<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->json('amenities')->nullable()->after('is_weekend_deal');
            $table->string('cancellation_policy', 500)->nullable()->after('amenities');
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->integer('max_guests')->default(2)->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn(['amenities', 'cancellation_policy']);
        });
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('max_guests');
        });
    }
};
