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
        if (!Schema::hasTable('partner_payouts')) return;

        if (Schema::hasColumn('partner_payouts', 'email_sent_at')) return;

        Schema::table('partner_payouts', function (Blueprint $table) {
            $table->timestamp('email_sent_at')->nullable()->after('paid_at');
        });
    }

    public function down(): void
    {
        Schema::table('partner_payouts', function (Blueprint $table) {
            $table->dropColumn('email_sent_at');
        });
    }
};
