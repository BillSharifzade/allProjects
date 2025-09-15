<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanPhonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_phones', function (Blueprint $table) {
            $table->id();
            $table->integer('loan_id');
            $table->string('brand');
            $table->string('model');
            $table->string('imei')->nullable();
            $table->integer('storage_gb')->nullable();
            $table->string('color')->nullable();
            $table->string('condition')->nullable();
            $table->string('description')->nullable();
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
        Schema::dropIfExists('loan_phones');
    }
}


