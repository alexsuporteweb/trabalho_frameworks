<?php

use App\Models\Divisoes;
use App\Models\Grupos;
use App\Models\Secoes;
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
        Schema::create('classes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('codigo', 10)->unique();
            $table->foreignIdFor(Grupos::class, 'grupo_id')
            ->constrained('grupos')
            ->onDelete('restrict')
            ->onUpdate('restrict');
            $table->foreignIdFor(Divisoes::class, 'divisao_id')
                ->constrained('divisoes')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->foreignIdFor(Secoes::class, 'secao_id')
                ->constrained('secoes')
                ->onDelete('restrict')
                ->onUpdate('restrict');
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
        Schema::dropIfExists('classes');
    }
};
