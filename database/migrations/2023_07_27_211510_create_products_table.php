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
            $table->unsignedBigInteger('caterogy_id');
            $table->foreign('caterogy_id')->references('id')->on('caterogy');
            $table->string('name');
            $table->float('price');
            $table->integer('count')->default(0);
            $table->unsignedBigInteger('type_id');
            $table->foreign('type_id')->references('id')->on('settings');
            $table->boolean('cyrrency')->default(0); // 0 - UZS 1 - USD
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('nbu_id')->default(0);
            $table->foreign('nbu_id')->references('id')->on('nbu');
            $table->boolean('type')->default(0); // 0 - icecream 1 - other
            $table->timestamps();
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
