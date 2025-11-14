<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->string('id')->primary();

            // your custom columns may go here
            $table->string('tenant_name');
            $table->string('tenant_industry');

            $table->string('global_layout')->default('standard');
            $table->string('global_style')->default('standard');

            $table->string('tenancy_db_name');
            $table->string('tenancy_db_driver');

            $table->timestamps();

            $table->index('tenant_name', 'tenant_name_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
}
