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
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'reminder_7day_sent_at')) {
                $table->timestamp('reminder_7day_sent_at')->nullable()->after('reminder_sent_at');
            }
            if (!Schema::hasColumn('bookings', 'morning_reminder_sent_at')) {
                $table->timestamp('morning_reminder_sent_at')->nullable()->after('reminder_7day_sent_at');
            }
            if (!Schema::hasColumn('bookings', 'survey_reminder_sent_at')) {
                $table->timestamp('survey_reminder_sent_at')->nullable()->after('survey_sent_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['reminder_7day_sent_at', 'morning_reminder_sent_at', 'survey_reminder_sent_at']);
        });
    }
};
