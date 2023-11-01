<?php

use App\Models\Regioes;
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
        Schema::create('estados', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sigla', 2)->unique();
            $table->string('nome', 120)->unique();
            $table->foreignIdFor(Regioes::class, 'regiao_id')
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
        Schema::dropIfExists('estados');
    }
};
