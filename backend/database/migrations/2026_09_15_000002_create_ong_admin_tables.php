<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('administradores', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 150);
            $table->string('email', 100)->unique();
            $table->string('senha');
            $table->timestamp('data_criacao')->useCurrent();
        });

        Schema::create('noticias', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 150);
            $table->string('resumo', 255);
            $table->text('texto_completo');
            $table->string('imagem')->nullable();
            $table->enum('tipo', ['noticia', 'evento', 'campanha'])->default('noticia');
            $table->timestamp('data_evento')->nullable();
            $table->timestamp('data_criacao')->useCurrent();
        });

        Schema::create('materiais_didaticos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 150);
            $table->text('descricao')->nullable();
            $table->string('arquivo_pdf');
            $table->string('imagem_capa')->nullable();
            $table->string('categoria', 100)->default('Neuropedagogia');
            $table->timestamp('data_upload')->useCurrent();
        });

        Schema::create('documentos_transparencia', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 150);
            $table->integer('ano_referencia');
            $table->enum('tipo_documento', ['Relatório Anual', 'Balancete', 'Estatuto', 'Certidão', 'Outros']);
            $table->string('arquivo_pdf');
            $table->timestamp('data_upload')->useCurrent();
        });

        Schema::create('newsletter', function (Blueprint $table) {
            $table->id();
            $table->string('email', 100)->unique();
            $table->string('nome', 100)->nullable();
            $table->timestamp('data_inscricao')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter');
        Schema::dropIfExists('documentos_transparencia');
        Schema::dropIfExists('materiais_didaticos');
        Schema::dropIfExists('noticias');
        Schema::dropIfExists('administradores');
    }
};