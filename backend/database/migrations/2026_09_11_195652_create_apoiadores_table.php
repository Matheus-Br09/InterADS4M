<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apoiadores', function (Blueprint $table) {
            $table->id();
            $table->string('nome_completo');
            $table->string('email')->unique();
            $table->string('senha');
            $table->string('cpf')->unique();
            $table->string('celular')->nullable();
            $table->string('sexo')->nullable();
            $table->string('cep')->nullable();
            $table->string('logradouro')->nullable();
            $table->string('numero')->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado')->nullable();
            $table->date('data_cadastro')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apoiadores');
    }
};