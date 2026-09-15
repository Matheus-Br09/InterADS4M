<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('criancas', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 150);
            $table->date('data_nascimento');
            $table->text('historico')->nullable();
            $table->string('imagem_perfil')->nullable();
            $table->enum('status', ['disponivel', 'apadrinhada'])->default('disponivel');
            $table->timestamp('data_cadastro')->useCurrent();
        });

        Schema::create('apadrinhamentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('apoiador_id');
            $table->unsignedBigInteger('crianca_id');
            $table->decimal('valor_mensal', 10, 2);
            $table->timestamp('data_inicio')->useCurrent();
            $table->enum('status', ['ativo', 'cancelado'])->default('ativo');

            $table->foreign('apoiador_id')->references('id')->on('apoiadores');
            $table->foreign('crianca_id')->references('id')->on('criancas');
        });

        Schema::create('recompensas_apadrinhamento', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('apadrinhamento_id');
            $table->string('titulo', 100);
            $table->text('mensagem')->nullable();
            $table->string('arquivo_midia');
            $table->timestamp('data_envio')->useCurrent();

            $table->foreign('apadrinhamento_id')->references('id')->on('apadrinhamentos');
        });

        Schema::create('doacoes_mensais', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('apoiador_id');
            $table->decimal('valor_mensal', 10, 2);
            $table->integer('dia_vencimento');
            $table->string('metodo_pagamento', 50)->default('Pix Automático');
            $table->string('status', 50)->default('ativo');
            $table->timestamp('data_assinatura')->useCurrent();

            $table->foreign('apoiador_id')->references('id')->on('apoiadores');
        });

        Schema::create('doacoes_unicas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('apoiador_id');
            $table->decimal('valor', 10, 2);
            $table->string('metodo_pagamento', 50);
            $table->string('status', 50)->default('concluido');
            $table->timestamp('data_doacao')->useCurrent();

            $table->foreign('apoiador_id')->references('id')->on('apoiadores');
        });

        Schema::create('galeria', function (Blueprint $table) {
            $table->id();
            $table->string('legenda', 150)->nullable();
            $table->string('nome_imagem');
            $table->timestamp('data_upload')->useCurrent();
        });

        Schema::create('programas_acoes', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 150);
            $table->string('resumo', 250);
            $table->text('texto_completo');
            $table->enum('categoria', ['Neuropedagogia', 'Saúde e Bem-estar', 'Assistência Social', 'Educação', 'Outros']);
            $table->string('imagem_capa');
            $table->enum('status', ['ativo', 'inativo'])->default('ativo');
            $table->timestamp('data_criacao')->useCurrent();
        });

        Schema::create('voluntarios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('apoiador_id');
            $table->enum('area_atuacao', ['Neuropedagogia', 'Odontologia', 'Nutrição', 'Fisioterapia', 'Apoio Geral', 'Outros']);
            $table->enum('disponibilidade', ['Manhã', 'Tarde', 'Integral']);
            $table->string('arquivo_curriculo')->default('');
            $table->enum('status', ['em_analise', 'entrevista_marcada', 'aprovado', 'recusado'])->default('em_analise');
            $table->timestamp('data_entrevista')->nullable();
            $table->text('mensagem_entrevista')->nullable();
            $table->timestamp('data_inscricao')->useCurrent();

            $table->foreign('apoiador_id')->references('id')->on('apoiadores');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voluntarios');
        Schema::dropIfExists('programas_acoes');
        Schema::dropIfExists('galeria');
        Schema::dropIfExists('doacoes_unicas');
        Schema::dropIfExists('doacoes_mensais');
        Schema::dropIfExists('recompensas_apadrinhamento');
        Schema::dropIfExists('apadrinhamentos');
        Schema::dropIfExists('criancas');
    }
};