<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('industry'); // e.g., 'advertising'
            $table->string('vendor'); // e.g., 'probo'
            $table->string('default_slug');

            $table->string('vendor_product_id'); // e.g., 'SKU12345'

            $table->string('category_level_1')->nullable(); // null for 'all products' category
            $table->string('category_level_2')->nullable(); // null means no second level
            $table->string('category_level_3')->nullable(); // null means no third level

            $table->json('catalog_index_data')->default('{}');

            $table->json('vendor_product_data')->nullable();
            $table->json('product_data_override')->nullable();

            $table->timestamp('last_fetch_at')->nullable();

            //created_at > 24h ago = visible product
            $table->timestamps();
            //todo: indexing on industry, category levels, vendor
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
