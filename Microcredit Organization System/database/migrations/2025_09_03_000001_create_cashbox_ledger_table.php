<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashboxLedgerTable extends Migration
{
    public function up()
    {
        Schema::create('cashbox_ledger', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->default(0);
            $table->integer('cashbox_id');
            $table->integer('user_id');
            $table->integer('shift_id')->default(0);
            $table->string('event_type'); // loan_disbursement, interest_payment, principal_payment, reversal, shift_open, shift_close
            $table->string('event_id')->nullable();
            $table->smallInteger('direction'); // +1 or -1
            $table->decimal('amount', 12, 2);
            $table->integer('occurred_at');
            $table->integer('created_at');
            $table->integer('updated_at')->default(0);
            $table->integer('deleted_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cashbox_ledger');
    }
}


