<?php

use App\Models\Estados;
use App\Models\Mesorregioes;
use App\Models\Microregioes;
use App\Models\Regioes;
use App\Models\RegioesImediatas;
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
        Schema::create('municipios', function (Blueprint $table) {
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
            $table->foreignIdFor(Mesorregioes::class, 'mesorregiao_id')
                ->constrained('mesorregioes')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->foreignIdFor(Microregioes::class, 'microregiao_id')
                ->constrained('microregioes')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->foreignIdFor(RegioesIntermediarias::class, 'regiao_intermediaria_id')
                ->constrained('regioes_intermediarias')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->foreignIdFor(RegioesImediatas::class, 'regiao_imediata_id')
                ->constrained('regioes_imediatas')
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
        Schema::dropIfExists('municipios');
    }
};
