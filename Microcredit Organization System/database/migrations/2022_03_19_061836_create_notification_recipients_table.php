<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationRecipientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_recipients', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->default(0);
            $table->integer('notification_id');
            $table->integer('loan_id');
            $table->integer('created_at');
            $table->integer('cancelled_at')->default(0);
            $table->integer('processed_at')->default(0);
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
        Schema::dropIfExists('notification_recipients');
    }
}
