<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'guest_country')) {
                $table->string('guest_country', 100)->nullable()->after('note');
            }
            if (!Schema::hasColumn('bookings', 'guest_country_code')) {
                $table->string('guest_country_code', 2)->nullable()->after('guest_country');
            }
            if (!Schema::hasColumn('bookings', 'guest_city')) {
                $table->string('guest_city', 100)->nullable()->after('guest_country_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['guest_country', 'guest_country_code', 'guest_city']);
        });
    }
};
