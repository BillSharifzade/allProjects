<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('cashbox_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('shift_id')->default(0);
            $table->unsignedBigInteger('loan_id');
            $table->unsignedInteger('sold_at');
            $table->decimal('amount_principal', 12, 2)->default(0);
            $table->decimal('amount_interest', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('event_id', 191)->unique();

            // Snapshot to allow clean reversal
            $table->decimal('prev_left_sum', 12, 2)->default(0);
            $table->unsignedInteger('prev_last_principal_payment_date')->default(0);
            $table->unsignedInteger('prev_last_interest_payment_date')->default(0);
            $table->decimal('prev_latest_interest_payments_sum', 12, 2)->default(0);

            $table->unsignedInteger('created_at');
            $table->unsignedInteger('updated_at')->nullable();

            // Cancellation metadata
            $table->unsignedInteger('canceled_at')->default(0);
            $table->unsignedBigInteger('canceled_by')->nullable();

            $table->index(['company_id', 'sold_at']);
            $table->index(['company_id', 'cashbox_id']);
            $table->index(['company_id', 'user_id']);
            $table->index(['company_id', 'loan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_sales');
    }
};


