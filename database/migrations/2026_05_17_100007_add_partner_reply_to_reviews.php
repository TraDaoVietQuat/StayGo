<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('reviews', 'partner_reply')) {
                $table->text('partner_reply')->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('reviews', 'partner_replied_at')) {
                $table->timestamp('partner_replied_at')->nullable()->after('partner_reply');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['partner_reply', 'partner_replied_at']);
        });
    }
};
