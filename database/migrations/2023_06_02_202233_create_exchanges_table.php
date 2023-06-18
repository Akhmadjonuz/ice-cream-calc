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
        Schema::create('exchanges', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->default(null);
            $table->unsignedBigInteger('partner_id');
            $table->foreign('partner_id')->references('id')->on('partners');
            $table->integer('value')->default(0);
            $table->string('type')->nullable()->default(null);
            $table->string('car')->nullable()->default(null);
            $table->integer('amount')->default(0);
            $table->integer('all_amount')->default(0);
            $table->integer('given_amount')->default(0);
            $table->boolean('other')->nullable()->default(false);
            $table->string('p_type')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchanges');
    }
};
