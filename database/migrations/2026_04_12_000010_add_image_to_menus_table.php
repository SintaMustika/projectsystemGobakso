<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('menus', 'image')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->string('image')->nullable()->after('price');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('menus', 'image')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }
    }
};
