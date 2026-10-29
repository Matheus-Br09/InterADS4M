<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('voluntarios', function (Blueprint $table) {
            // Nulo para as inscrições antigas, feitas antes da regra de maioridade.
            $table->date('data_nascimento')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('voluntarios', function (Blueprint $table) {
            $table->dropColumn('data_nascimento');
        });
    }
};
