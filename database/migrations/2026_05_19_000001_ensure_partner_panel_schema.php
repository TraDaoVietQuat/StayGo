<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Safety migration: ensures all partner-panel tables and columns exist.
 * Uses hasTable()/hasColumn() guards throughout so it is fully idempotent.
 * Runs even when earlier 2026-05-17 migrations were recorded as done but
 * failed to apply their DDL (e.g. after a partial Railway deploy).
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── hotel_partner_profiles ──────────────────────────────────────────
        if (!Schema::hasTable('hotel_partner_profiles')) {
            Schema::create('hotel_partner_profiles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
                $table->enum('status', ['pending', 'active', 'suspended', 'rejected'])->default('pending');
                $table->decimal('commission_rate', 5, 2)->default(15.00);
                $table->string('business_name', 150)->nullable();
                $table->string('contact_name', 100)->nullable();
                $table->string('contact_phone', 20)->nullable();
                $table->string('tax_code', 30)->nullable();
                $table->string('bank_name', 100)->nullable();
                $table->string('bank_account', 50)->nullable();
                $table->string('bank_branch', 150)->nullable();
                $table->string('bank_owner', 100)->nullable();
                $table->text('notes')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('approved_at')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            });
        }

        // review_checklist columns in hotel_partner_profiles
        if (!Schema::hasColumn('hotel_partner_profiles', 'review_checklist')) {
            Schema::table('hotel_partner_profiles', function (Blueprint $table) {
                $table->json('review_checklist')->nullable()->after('rejection_reason');
                $table->string('review_decision', 30)->nullable()->after('review_checklist');
                $table->text('review_summary')->nullable()->after('review_decision');
                $table->text('review_missing_items')->nullable()->after('review_summary');
                $table->decimal('proposed_commission', 5, 2)->nullable()->after('review_missing_items');
                $table->text('review_notes')->nullable()->after('proposed_commission');
            });
        }
        if (!Schema::hasColumn('hotel_partner_profiles', 'reviewed_by')) {
            Schema::table('hotel_partner_profiles', function (Blueprint $table) {
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete()->after('review_notes');
                $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            });
        }

        // ── hotels.partner_user_id ─────────────────────────────────────────
        if (!Schema::hasColumn('hotels', 'partner_user_id')) {
            Schema::table('hotels', function (Blueprint $table) {
                $table->foreignId('partner_user_id')->nullable()->after('id')
                    ->constrained('users')->nullOnDelete();
            });
        }

        // ── partner_payouts ────────────────────────────────────────────────
        if (!Schema::hasTable('partner_payouts')) {
            Schema::create('partner_payouts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('partner_user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
                $table->date('period_start');
                $table->date('period_end');
                $table->decimal('gross_revenue', 14, 2)->default(0);
                $table->decimal('commission_rate', 5, 2)->default(15.00);
                $table->decimal('commission_amount', 14, 2)->default(0);
                $table->decimal('net_amount', 14, 2)->default(0);
                $table->integer('booking_count')->default(0);
                $table->enum('status', ['pending', 'processing', 'paid', 'cancelled'])->default('pending');
                $table->string('transfer_ref', 100)->nullable();
                $table->text('note')->nullable();
                $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('paid_at')->nullable();
                $table->timestamp('email_sent_at')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            });
        }
        if (!Schema::hasColumn('partner_payouts', 'email_sent_at')) {
            Schema::table('partner_payouts', function (Blueprint $table) {
                $table->timestamp('email_sent_at')->nullable()->after('paid_at');
            });
        }

        // ── room_prices ────────────────────────────────────────────────────
        if (!Schema::hasTable('room_prices')) {
            Schema::create('room_prices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
                $table->date('start_date');
                $table->date('end_date');
                $table->decimal('price', 12, 2);
                $table->string('label', 100)->nullable();
                $table->enum('price_type', ['custom', 'holiday', 'weekend', 'early_bird', 'last_minute'])->default('custom');
                $table->timestamp('created_at')->useCurrent();
            });
        }

        // ── room_unavailable_dates ─────────────────────────────────────────
        if (!Schema::hasTable('room_unavailable_dates')) {
            Schema::create('room_unavailable_dates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
                $table->date('date');
                $table->string('reason', 150)->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->unique(['room_id', 'date']);
            });
        }

        // ── reviews: partner_reply, sub-scores ────────────────────────────
        Schema::table('reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('reviews', 'partner_reply')) {
                $table->text('partner_reply')->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('reviews', 'partner_replied_at')) {
                $table->timestamp('partner_replied_at')->nullable()->after('partner_reply');
            }
            if (!Schema::hasColumn('reviews', 'cleanliness')) {
                $table->decimal('cleanliness', 3, 1)->nullable()->after('rating');
            }
            if (!Schema::hasColumn('reviews', 'service_score')) {
                $table->decimal('service_score', 3, 1)->nullable()->after('cleanliness');
            }
            if (!Schema::hasColumn('reviews', 'location_score')) {
                $table->decimal('location_score', 3, 1)->nullable()->after('service_score');
            }
            if (!Schema::hasColumn('reviews', 'value_score')) {
                $table->decimal('value_score', 3, 1)->nullable()->after('location_score');
            }
        });

        // ── bookings: email tracking + geo fields ─────────────────────────
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
            if (!Schema::hasColumn('bookings', 'guest_country')) {
                $table->string('guest_country', 100)->nullable()->after('note');
            }
            if (!Schema::hasColumn('bookings', 'guest_country_code')) {
                $table->string('guest_country_code', 2)->nullable()->after('guest_country');
            }
            if (!Schema::hasColumn('bookings', 'guest_city')) {
                $table->string('guest_city', 100)->nullable()->after('guest_country_code');
            }
        });

        // ── disputes ──────────────────────────────────────────────────────
        if (!Schema::hasTable('disputes')) {
            Schema::create('disputes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('hotel_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('support_request_id')->nullable()->constrained('support_requests')->nullOnDelete();
                $table->string('type', 20);
                $table->string('priority', 10)->default('normal');
                $table->string('status', 30)->default('open');
                $table->string('title');
                $table->text('description');
                $table->text('timeline')->nullable();
                $table->text('hotel_response')->nullable();
                $table->json('evidence')->nullable();
                $table->string('fault_party', 20)->nullable();
                $table->text('fault_details')->nullable();
                $table->text('ai_analysis')->nullable();
                $table->string('ai_recommendation', 30)->nullable();
                $table->timestamp('ai_analyzed_at')->nullable();
                $table->string('verdict', 30)->nullable();
                $table->text('verdict_details')->nullable();
                $table->decimal('refund_amount', 12, 2)->nullable();
                $table->decimal('refund_percentage', 5, 2)->nullable();
                $table->decimal('voucher_amount', 12, 2)->nullable();
                $table->boolean('requires_supervisor')->default(false);
                $table->foreignId('supervisor_approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('supervisor_approved_at')->nullable();
                $table->boolean('penalty_applied')->default(false);
                $table->text('penalty_details')->nullable();
                $table->timestamp('customer_notified_at')->nullable();
                $table->timestamp('hotel_notified_at')->nullable();
                $table->boolean('faq_updated')->default(false);
                $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('resolved_at')->nullable();
                $table->timestamp('deadline_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Safety migration — intentionally not rolled back to avoid data loss
    }
};
