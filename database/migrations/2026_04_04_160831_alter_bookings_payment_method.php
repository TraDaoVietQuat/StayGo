<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE bookings MODIFY payment_method ENUM('bank','momo','vnpay','card','hotel') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE bookings MODIFY payment_method ENUM('bank','momo','vnpay') NOT NULL");
    }
};
