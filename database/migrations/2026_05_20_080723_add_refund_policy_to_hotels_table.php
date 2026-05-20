<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->enum('refund_policy', ['flexible', 'moderate', 'strict', 'non_refundable'])
                  ->default('moderate')
                  ->after('cancellation_policy')
                  ->comment('flexible=hoàn 100% trước 24h; moderate=hoàn 100% trước 5 ngày; strict=hoàn 50% trước 7 ngày; non_refundable=không hoàn');
        });
    }

    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn('refund_policy');
        });
    }
};
