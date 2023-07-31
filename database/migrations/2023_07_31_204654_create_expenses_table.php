<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('material_id');
            $table->foreign('material_id')->references('id')->on('products');
            $table->float('value')->default(0);
            $table->unsignedBigInteger('type_id')->default(0);
            $table->foreign('type_id')->references('id')->on('settings');
            $table->float('price_uzs')->default(0);
            $table->float('price_usd')->default(0);
            $table->float('count')->default(0);
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};