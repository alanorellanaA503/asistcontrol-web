<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuario', function (Blueprint $table) {
            $table->boolean('debe_cambiar_password')->default(true);
        });

        DB::table('usuario')
            ->where('rol', 'ADMINISTRADOR')
            ->update(['debe_cambiar_password' => false]);
    }

    public function down(): void
    {
        Schema::table('usuario', function (Blueprint $table) {
            $table->dropColumn('debe_cambiar_password');
        });
    }
};
