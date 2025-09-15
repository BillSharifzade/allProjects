<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blacklist_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('passport_id_norm', 64);
            $table->string('full_name')->nullable();
            $table->string('phone')->nullable();
            $table->json('raw_json')->nullable();
            $table->unsignedInteger('created_at')->default(0);
            $table->unsignedInteger('updated_at')->default(0);

            $table->index(['company_id']);
            $table->unique(['company_id', 'passport_id_norm'], 'uniq_company_passport');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blacklist_entries');
    }
};


