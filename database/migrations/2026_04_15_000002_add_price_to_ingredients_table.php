<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('ingredients', 'price')) {
            Schema::table('ingredients', function (Blueprint $table) {
                $table->decimal('price', 12, 2)->default(0)->after('item_name');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('ingredients', 'price')) {
            Schema::table('ingredients', function (Blueprint $table) {
                $table->dropColumn('price');
            });
        }
    }
};
