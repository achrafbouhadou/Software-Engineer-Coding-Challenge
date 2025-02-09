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
        Schema::table('products', function (Blueprint $table) {
            $table->index('name');
            $table->index('price');
            $table->index('created_at');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index('name');
            $table->index('parent_id');
        });

        Schema::table('category_product', function (Blueprint $table) {
            $table->index(['product_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['price']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['parent_id']);
        });

        Schema::table('category_product', function (Blueprint $table) {
            $table->dropIndex(['product_id', 'category_id']);
        });
    }
};
