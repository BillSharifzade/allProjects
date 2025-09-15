<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incassator_safe_loans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('incassator_id');
            $table->unsignedBigInteger('cashbox_id')->nullable();
            $table->string('contract_no', 64);
            $table->string('client_name', 191);
            $table->text('loan_info')->nullable();
            $table->unsignedInteger('created_at');
            $table->unsignedInteger('updated_at')->nullable();
            $table->index(['company_id','incassator_id']);
            $table->index(['company_id','cashbox_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incassator_safe_loans');
    }
};


