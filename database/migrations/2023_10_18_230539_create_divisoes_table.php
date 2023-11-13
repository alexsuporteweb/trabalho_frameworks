<?php

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
        Schema::create('divisoes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('codigo', 5)->unique();
            $table->foreignIdFor(Secao::class, 'secao_id')
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
        Schema::dropIfExists('divisoes');
    }
};
