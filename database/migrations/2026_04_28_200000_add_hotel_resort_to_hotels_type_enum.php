<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE hotels MODIFY COLUMN type ENUM('hotel','hotel_resort','homestay','resort') NOT NULL DEFAULT 'hotel'");
    }

    public function down(): void
    {
        // Đổi các hotel_resort về hotel trước khi xóa giá trị khỏi ENUM
        DB::statement("UPDATE hotels SET type = 'hotel' WHERE type = 'hotel_resort'");
        DB::statement("ALTER TABLE hotels MODIFY COLUMN type ENUM('hotel','homestay','resort') NOT NULL DEFAULT 'hotel'");
    }
};
