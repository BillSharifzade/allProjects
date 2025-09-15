<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('loan_sales')) { return; }
        Schema::table('loan_sales', function (Blueprint $table) {
            if (!Schema::hasColumn('loan_sales', 'price_375')) {
                $table->decimal('price_375', 12, 2)->default(0)->after('total_amount');
            }
            if (!Schema::hasColumn('loan_sales', 'price_585')) {
                $table->decimal('price_585', 12, 2)->default(0)->after('price_375');
            }
            if (!Schema::hasColumn('loan_sales', 'price_750')) {
                $table->decimal('price_750', 12, 2)->default(0)->after('price_585');
            }
            if (!Schema::hasColumn('loan_sales', 'price_875')) {
                $table->decimal('price_875', 12, 2)->default(0)->after('price_750');
            }
            if (!Schema::hasColumn('loan_sales', 'proceeds_amount')) {
                $table->decimal('proceeds_amount', 14, 2)->default(0)->after('price_875');
            }
            if (!Schema::hasColumn('loan_sales', 'profit_amount')) {
                $table->decimal('profit_amount', 14, 2)->default(0)->after('proceeds_amount');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('loan_sales')) { return; }
        Schema::table('loan_sales', function (Blueprint $table) {
            foreach (['price_375','price_585','price_750','price_875','proceeds_amount','profit_amount'] as $col) {
                if (Schema::hasColumn('loan_sales', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};


