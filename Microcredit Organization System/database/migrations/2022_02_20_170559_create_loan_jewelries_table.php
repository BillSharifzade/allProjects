<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanJewelriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_jewelries', function (Blueprint $table) {
            $table->id();
            $table->integer('loan_id');
            $table->string('name');
            $table->integer('purity');
            $table->decimal('weight', 10, 2);
            $table->decimal('pure_weight', 10, 2);
            $table->integer('count');
            $table->decimal('price', 10, 2);
            $table->string('notes')->nullable();
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
        Schema::dropIfExists('loan_jewelries');
    }
}
