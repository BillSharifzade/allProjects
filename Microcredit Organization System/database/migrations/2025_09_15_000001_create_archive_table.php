<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchiveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('archive', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('company_id')->default(0)->index();
            $table->integer('loan_id')->default(0)->index();
            $table->string('type', 32)->index(); // closed | deleted
            $table->longText('snapshot');
            $table->integer('archived_at')->default(0)->index();
            $table->integer('created_at')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('archive');
    }
}


