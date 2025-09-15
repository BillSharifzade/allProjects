<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loaners', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->default(0);
            $table->integer('cashbox_id');
            $table->string('full_name');
            $table->string('phone1');
            $table->string('phone2')->nullable();
            $table->string('phone3')->nullable();
            $table->string('phone4')->nullable();
            $table->string('tin');
            $table->string('passport_number');
            $table->string('passport_issuer');
            $table->integer('passport_issued_day');
            $table->integer('passport_issued_month');
            $table->integer('passport_issued_year');
            $table->integer('birth_day');
            $table->integer('birth_month');
            $table->integer('birth_year');
            $table->string('residence_address');
            $table->integer('created_at');
            $table->integer('updated_at')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loaners');
    }
}
