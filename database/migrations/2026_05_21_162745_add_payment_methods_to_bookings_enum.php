<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE bookings MODIFY payment_method ENUM('bank','momo','vnpay','card','hotel','zalopay','cod','bank_transfer','vietqr','wallet') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE bookings MODIFY payment_method ENUM('bank','momo','vnpay','card','hotel') NOT NULL");
    }
};
