<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ẩn 3 khách sạn Nha Trang cho đến khi có ảnh thực tế
        DB::table('hotels')->whereIn('id', [4, 5, 6])->update(['is_active' => false]);
    }

    public function down(): void
    {
        DB::table('hotels')->whereIn('id', [4, 5, 6])->update(['is_active' => true]);
    }
};
