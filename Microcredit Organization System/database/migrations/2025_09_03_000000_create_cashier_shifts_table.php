<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashierShiftsTable extends Migration
{
    public function up()
    {
        Schema::create('cashier_shifts', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->default(0);
            $table->integer('cashbox_id');
            $table->integer('user_id');
            $table->integer('opened_at');
            $table->integer('closed_at')->default(0);
            $table->decimal('opening_balance', 12, 2)->default(0);
            $table->decimal('closing_balance', 12, 2)->default(0);
            $table->decimal('discrepancy', 12, 2)->default(0);
            $table->string('note')->nullable();
            $table->integer('created_at');
            $table->integer('updated_at')->default(0);
            $table->integer('deleted_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cashier_shifts');
    }
}


