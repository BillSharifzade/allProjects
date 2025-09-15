<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashboxUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashbox_users', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->default(0);
            $table->integer('cashbox_id');
            $table->integer('user_id');
            $table->string('user_license')->nullable();
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
        Schema::dropIfExists('cashbox_users');
    }
}
