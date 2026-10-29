<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // O gestor da ONG não precisa informar CPF. O índice único continua:
        // vários valores nulos são permitidos, CPFs repetidos não.
        Schema::table('apoiadores', function (Blueprint $table) {
            $table->string('cpf')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('apoiadores', function (Blueprint $table) {
            $table->string('cpf')->nullable(false)->change();
        });
    }
};
