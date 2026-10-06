<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('apadrinhamentos', function (Blueprint $table) {
            $table->enum('status', ['ativo', 'cancelado', 'pendente'])->default('ativo')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::table('apadrinhamentos')->where('status', 'pendente')->exists()) {
            throw new RuntimeException('Resolva os apadrinhamentos pendentes antes de reverter esta migração.');
        }

        Schema::table('apadrinhamentos', function (Blueprint $table) {
            $table->enum('status', ['ativo', 'cancelado'])->default('ativo')->change();
        });
    }
};
