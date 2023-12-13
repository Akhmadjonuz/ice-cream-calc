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
        Schema::create('products_price_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')
                ->references('id')->on('products');
            $table->float('price')->nullable()->default(0);
            $table->float('price_uzs')->nullable()->default(0);
            $table->float('price_usd')->nullable()->default(0);
            $table->unsignedBigInteger('nbu_id');
            $table->foreign('nbu_id')
                ->references('id')->on('nbu');
            $table->float('count')->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products_price_log');
    }
};
