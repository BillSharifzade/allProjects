<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->default(0);
            $table->integer('cashbox_id');
            $table->integer('user_id');
            $table->integer('loan_id');
            $table->string('uuid');
            $table->smallInteger('type');
            $table->decimal('sum', 10, 2);
            $table->integer('paid_date');
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
        Schema::dropIfExists('payments');
    }
}
