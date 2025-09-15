<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('hr_employees')) {
            Schema::create('hr_employees', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('company_id');
                $table->string('first_name');
                $table->string('last_name');
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('passport_number')->nullable();
                $table->string('photo')->nullable();
                $table->string('position')->nullable();
                $table->boolean('active')->default(true);
                $table->integer('created_at')->default(0);
                $table->integer('updated_at')->default(0);
                $table->softDeletesTz('deleted_at');
                $table->index(['company_id','last_name']);
            });
        }

        if (!Schema::hasTable('hr_employee_contracts')) {
            Schema::create('hr_employee_contracts', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('company_id');
                $table->unsignedBigInteger('employee_id');
                $table->string('contract_no')->nullable();
                $table->integer('start_date');
                $table->integer('end_date')->default(0); // 0 = indefinite / active
                $table->decimal('salary', 14, 2)->default(0);
                $table->string('currency', 8)->default('TJS');
                $table->string('notes')->nullable();
                $table->integer('created_at')->default(0);
                $table->integer('updated_at')->default(0);
                $table->softDeletesTz('deleted_at');
                $table->index(['company_id','employee_id']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('hr_employee_contracts')) {
            Schema::drop('hr_employee_contracts');
        }
        if (Schema::hasTable('hr_employees')) {
            Schema::drop('hr_employees');
        }
    }
};


