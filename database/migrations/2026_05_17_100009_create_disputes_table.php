<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disputes', function (Blueprint $table) {
            $table->id();

            // Links
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('hotel_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('support_request_id')->nullable()->constrained('support_requests')->nullOnDelete();

            // Classification
            $table->string('type', 20);
            // no_show | overbooking | quality | hidden_fees | slow_refund | misconduct
            $table->string('priority', 10)->default('normal'); // normal | urgent
            $table->string('status', 30)->default('open');
            // open | investigating | pending_hotel | pending_customer | resolved | closed | escalated

            // Step 1 — Information
            $table->string('title');
            $table->text('description');
            $table->text('timeline')->nullable();
            $table->text('hotel_response')->nullable();
            $table->json('evidence')->nullable();

            // Step 2 — Responsibility
            $table->string('fault_party', 20)->nullable();
            // hotel | customer | platform | third_party | mixed
            $table->text('fault_details')->nullable();

            // AI Analysis
            $table->text('ai_analysis')->nullable();
            $table->string('ai_recommendation', 30)->nullable();
            $table->timestamp('ai_analyzed_at')->nullable();

            // Step 3 — Verdict
            $table->string('verdict', 30)->nullable();
            // refund_full | refund_partial | voucher | rejected | hotel_compensate
            $table->text('verdict_details')->nullable();
            $table->decimal('refund_amount', 12, 2)->nullable();
            $table->decimal('refund_percentage', 5, 2)->nullable();
            $table->decimal('voucher_amount', 12, 2)->nullable();
            $table->boolean('requires_supervisor')->default(false);
            $table->foreignId('supervisor_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('supervisor_approved_at')->nullable();

            // Step 4 — Follow-up
            $table->boolean('penalty_applied')->default(false);
            $table->text('penalty_details')->nullable();
            $table->timestamp('customer_notified_at')->nullable();
            $table->timestamp('hotel_notified_at')->nullable();
            $table->boolean('faq_updated')->default(false);

            // Assignment
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('deadline_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disputes');
    }
};
