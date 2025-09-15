<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('incassator_safe_loans') && !Schema::hasColumn('incassator_safe_loans', 'loan_id')) {
            Schema::table('incassator_safe_loans', function (Blueprint $table) {
                $table->unsignedBigInteger('loan_id')->nullable()->after('cashbox_id');
                $table->index(['company_id','incassator_id','loan_id'], 'inc_safe_company_inc_loan_idx');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('incassator_safe_loans') && Schema::hasColumn('incassator_safe_loans', 'loan_id')) {
            Schema::table('incassator_safe_loans', function (Blueprint $table) {
                $table->dropIndex('inc_safe_company_inc_loan_idx');
                $table->dropColumn('loan_id');
            });
        }
    }
};


