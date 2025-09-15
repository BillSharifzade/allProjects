<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesAndConstraints extends Migration
{
    public function up()
    {
        // cashbox_ledger indexes and idempotency unique
        Schema::table('cashbox_ledger', function (Blueprint $table) {
            if (!Schema::hasColumn('cashbox_ledger', 'event_id')) {
                return;
            }
            $table->unique(['company_id','event_type','event_id'], 'cashbox_ledger_event_unique');
            $table->index(['company_id','occurred_at'], 'cashbox_ledger_company_occurred_idx');
            $table->index(['event_type','user_id','occurred_at'], 'cashbox_ledger_ev_user_occ_idx');
            $table->index('shift_id', 'cashbox_ledger_shift_idx');
            $table->index('event_id', 'cashbox_ledger_eventid_idx');
        });

        // payments
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'paid_date')) {
                return;
            }
            $table->index(['loan_id','paid_date'], 'payments_loan_paid_idx');
            $table->index(['type','paid_date'], 'payments_type_paid_idx');
            $table->index(['company_id','cashbox_id','paid_date'], 'payments_company_cashbox_paid_idx');
        });

        // loans
        Schema::table('loans', function (Blueprint $table) {
            if (!Schema::hasColumn('loans', 'closed_at')) {
                return;
            }
            $table->index(['cashbox_id','closed_at'], 'loans_cashbox_closed_idx');
            $table->index(['in_audit','lend_date'], 'loans_audit_lend_idx');
        });

        // expenses
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'occurred_at')) {
                return;
            }
            $table->index(['company_id','occurred_at'], 'expenses_company_occurred_idx');
            $table->index(['user_id','occurred_at'], 'expenses_user_occurred_idx');
            $table->index(['cashbox_id','occurred_at'], 'expenses_cashbox_occurred_idx');
        });

        // incassation transfers
        Schema::table('incassation_transfers', function (Blueprint $table) {
            if (Schema::hasTable('incassation_transfers')) {
                // fast filters for role-based pages
                $table->index(['company_id','delivered_by_incassator','accepted_by_cashier'], 'inc_x_company_deliv_acc_idx');
                $table->index(['company_id','cashbox_id','delivered_by_incassator','accepted_by_cashier'], 'inc_x_company_cashbox_state_idx');
                // prevent accidental duplicates per loan
                if (Schema::hasColumn('incassation_transfers', 'loan_id')) {
                    $table->unique(['company_id','loan_id'], 'inc_x_company_loan_unique');
                }
                // created_at range filter for admin
                $table->index(['company_id','created_at'], 'inc_x_company_created_idx');
            }
        });
    }

    public function down()
    {
        Schema::table('cashbox_ledger', function (Blueprint $table) {
            $table->dropUnique('cashbox_ledger_event_unique');
            $table->dropIndex('cashbox_ledger_company_occurred_idx');
            $table->dropIndex('cashbox_ledger_ev_user_occ_idx');
            $table->dropIndex('cashbox_ledger_shift_idx');
            $table->dropIndex('cashbox_ledger_eventid_idx');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_loan_paid_idx');
            $table->dropIndex('payments_type_paid_idx');
            $table->dropIndex('payments_company_cashbox_paid_idx');
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->dropIndex('loans_cashbox_closed_idx');
            $table->dropIndex('loans_audit_lend_idx');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex('expenses_company_occurred_idx');
            $table->dropIndex('expenses_user_occurred_idx');
            $table->dropIndex('expenses_cashbox_occurred_idx');
        });

        Schema::table('incassation_transfers', function (Blueprint $table) {
            $table->dropIndex('inc_x_company_deliv_acc_idx');
            $table->dropIndex('inc_x_company_cashbox_state_idx');
            $table->dropIndex('inc_x_company_created_idx');
            $table->dropUnique('inc_x_company_loan_unique');
        });
    }
}


