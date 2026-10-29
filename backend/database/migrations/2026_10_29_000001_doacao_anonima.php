<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doacoes_unicas', function (Blueprint $table) {
            $table->unsignedBigInteger('apoiador_id')->nullable()->change();
            $table->string('nome_doador', 255)->nullable();
            $table->string('email_doador', 255)->nullable();
            $table->string('protocolo', 25)->nullable()->unique();
            $table->string('status', 50)->default('pendente')->change();
        });
    }

    public function down(): void
    {
        Schema::table('doacoes_unicas', function (Blueprint $table) {
            $table->dropUnique(['protocolo']);
            $table->dropColumn(['nome_doador', 'email_doador', 'protocolo']);
            $table->string('status', 50)->default('concluido')->change();
            $table->unsignedBigInteger('apoiador_id')->nullable(false)->change();
        });
    }
};
