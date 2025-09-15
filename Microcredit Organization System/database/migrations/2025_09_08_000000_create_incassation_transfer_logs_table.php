<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('incassation_transfer_logs')) {
            Schema::create('incassation_transfer_logs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('company_id');
                $table->unsignedBigInteger('incassation_transfer_id');
                $table->unsignedBigInteger('actor_user_id');
                $table->string('action', 32); // pick | deliver | accept | reset
                $table->boolean('picked_by_incassator')->default(false);
                $table->boolean('delivered_by_incassator')->default(false);
                $table->boolean('accepted_by_cashier')->default(false);
                $table->unsignedInteger('created_at');
            });
        }

        // Ensure index exists with short name; safe to run if table was pre-created
        $hasIdx = collect(DB::select("SHOW INDEX FROM incassation_transfer_logs WHERE Key_name = 'inc_log_company_transfer_idx'"))->count() > 0;
        if (!$hasIdx) {
            Schema::table('incassation_transfer_logs', function (Blueprint $table) {
                $table->index(['company_id','incassation_transfer_id'], 'inc_log_company_transfer_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('incassation_transfer_logs');
    }
};


