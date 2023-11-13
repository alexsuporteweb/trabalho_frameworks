<?php

use App\Models\Classe;
use App\Models\Divisao;
use App\Models\Grupo;
use App\Models\Secao;
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
        Schema::create('subclasses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('codigo', 10)->unique();
            $table->foreignIdFor(Classe::class, 'classe_id')
                ->constrained('classes')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->foreignIdFor(Grupo::class, 'grupo_id')
                ->constrained('grupos')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->foreignIdFor(Divisao::class, 'divisao_id')
                ->constrained('divisoes')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->foreignIdFor(Secao::class, 'secao_id')
                ->constrained('secoes')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->text('atividades', 255)->nullable();
            $table->string('descricao', 255);
            $table->text('observacoes')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subclasses');
    }
};
