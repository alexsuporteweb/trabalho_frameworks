<?php

use App\Models\Estados;
use App\Models\Regioes;
use App\Models\RegioesIntermediarias;
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
        Schema::create('regioes_imediatas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nome', 120)->index();
            $table->foreignIdFor(Estados::class, 'estado_id')
                ->constrained('estados')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->foreignIdFor(Regioes::class, 'regiao_id')
                ->constrained('regioes')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->foreignIdFor(RegioesIntermediarias::class, 'regiao_intermediaria_id')
                ->constrained('regioes_intermediarias')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regioes_imediatas');
    }
};
