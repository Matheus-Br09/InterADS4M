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
        Schema::table('voluntarios', function (Blueprint $table) {
            // Permitir cadastro anônimo (sem apoiador logado)
            $table->unsignedBigInteger('apoiador_id')->nullable()->change();
            
            // Campos diretos para cadastro sem autenticação
            $table->string('nome_completo')->nullable()->after('apoiador_id');
            $table->string('email')->nullable()->after('nome_completo');
            $table->string('celular', 20)->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voluntarios', function (Blueprint $table) {
            $table->dropColumn(['nome_completo', 'email', 'celular']);
            $table->unsignedBigInteger('apoiador_id')->nullable(false)->change();
        });
    }
};
