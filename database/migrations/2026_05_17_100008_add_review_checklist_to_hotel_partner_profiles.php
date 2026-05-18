<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotel_partner_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('hotel_partner_profiles', 'review_checklist')) {
                $table->json('review_checklist')->nullable()->after('rejection_reason');
                $table->string('review_decision', 30)->nullable()->after('review_checklist');
                $table->text('review_summary')->nullable()->after('review_decision');
                $table->text('review_missing_items')->nullable()->after('review_summary');
                $table->decimal('proposed_commission', 5, 2)->nullable()->after('review_missing_items');
                $table->text('review_notes')->nullable()->after('proposed_commission');
                if (!Schema::hasColumn('hotel_partner_profiles', 'reviewed_by')) {
                    $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete()->after('review_notes');
                }
                $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hotel_partner_profiles', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn([
                'review_checklist', 'review_decision', 'review_summary',
                'review_missing_items', 'proposed_commission', 'review_notes',
                'reviewed_by', 'reviewed_at',
            ]);
        });
    }
};
