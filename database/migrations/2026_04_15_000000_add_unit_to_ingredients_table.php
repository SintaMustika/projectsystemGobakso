<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('ingredients', 'unit')) {
            Schema::table('ingredients', function (Blueprint $table) {
                $table->string('unit')->nullable()->after('stock_quantity');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('ingredients', 'unit')) {
            Schema::table('ingredients', function (Blueprint $table) {
                $table->dropColumn('unit');
            });
        }
    }
};
