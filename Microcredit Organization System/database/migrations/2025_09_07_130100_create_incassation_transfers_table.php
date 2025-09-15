<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incassation_transfers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('cashbox_id');
            $table->unsignedBigInteger('incassator_id')->nullable();
            $table->unsignedBigInteger('cashier_id')->nullable();
            $table->unsignedBigInteger('loan_id')->nullable();
            $table->string('contract_no', 64);
            $table->string('client_name', 191);
            $table->text('loan_info')->nullable();
            $table->boolean('picked_by_incassator')->default(false);
            $table->unsignedInteger('picked_at')->default(0);
            $table->boolean('delivered_by_incassator')->default(false);
            $table->unsignedInteger('delivered_at')->default(0);
            $table->boolean('accepted_by_cashier')->default(false);
            $table->unsignedInteger('accepted_at')->default(0);
            $table->unsignedInteger('created_at');
            $table->unsignedInteger('updated_at')->nullable();
            $table->index(['company_id','cashbox_id']);
            $table->index(['company_id','incassator_id']);
            $table->index(['company_id','accepted_by_cashier']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incassation_transfers');
    }
};


