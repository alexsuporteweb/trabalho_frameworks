<?php

use App\Models\Estado;
use App\Models\Mesorregiao;
use App\Models\Regiao;
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
        Schema::create('microrregioes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nome', 120)->index();
            $table->foreignIdFor(Mesorregiao::class, 'mesorregiao_id')
                ->constrained('mesorregioes')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->foreignIdFor(Estado::class, 'estado_id')
                ->constrained('estados')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->foreignIdFor(Regiao::class, 'regiao_id')
                ->constrained('regioes')
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
        Schema::dropIfExists('microrregioes');
    }
};
