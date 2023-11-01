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
        Schema::create('paises', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('m49')->index();
            $table->string('iso_alpha_2', 30)->index();
            $table->string('iso_alpha_3', 30)->index();
            $table->string('nome', 120)->index();
            $table->bigInteger('regiao_intermediaria_m49')->nullable()->index();
            $table->string('regiao_intermediaria', 120)->nullable()->nullable()->index();
            $table->bigInteger('sub_regiao_m49')->index();
            $table->string('sub_regiao', 120)->index();
            $table->bigInteger('regiao_m49')->index();
            $table->string('regiao', 120)->index();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paises');
    }
};
