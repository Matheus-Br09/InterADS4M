<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('apoiadores', function (Blueprint $table) {
            $table->boolean('trocar_senha_obrigatorio')->default(false)->after('senha_alterada_em');
        });
    }

    public function down(): void
    {
        Schema::table('apoiadores', function (Blueprint $table) {
            $table->dropColumn('trocar_senha_obrigatorio');
        });
    }
};
