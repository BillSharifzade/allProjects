<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->default(0);
            $table->integer('cashbox_id');
            $table->integer('user_id');
            $table->integer('loaner_id');
            $table->integer('document_no');
            $table->integer('audit_document_no')->default(0);
            $table->integer('lend_date');
            $table->integer('last_principal_payment_date')->default(0);
            $table->integer('last_interest_payment_date')->default(0);
            $table->integer('latest_interest_payments_sum')->default(0);
            $table->decimal('initial_sum', 10, 2);
            $table->decimal('left_sum', 10, 2)->default(0.00);
            $table->decimal('interestRate', 10, 2);
            $table->integer('interest_accumulation_date');
            $table->integer('close_request_at')->default(0);
            $table->integer('closed_at')->default(0);
            $table->string('image')->nullable();
            $table->json('props')->nullable();
            $table->boolean('in_audit')->default(false);
            $table->boolean('is_notifiable')->default(false);
            $table->integer('created_at');
            $table->integer('updated_at')->default(0);
            $table->integer('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loans');
    }
}
