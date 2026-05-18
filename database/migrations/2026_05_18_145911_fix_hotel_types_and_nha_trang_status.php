<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Fix type 'hotel_resort' -> 'resort' (non-standard value)
        DB::table('hotels')
            ->where('type', 'hotel_resort')
            ->update(['type' => 'resort']);

        // Enable Nha Trang hotels (họ bị tắt is_active=0)
        DB::table('hotels')
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                  ->from('locations')
                  ->whereColumn('locations.id', 'hotels.location_id')
                  ->where('locations.name', 'Nha Trang');
            })
            ->update(['is_active' => true]);
    }

    public function down(): void
    {
        // Không rollback dữ liệu vì đây là data fix
    }
};
