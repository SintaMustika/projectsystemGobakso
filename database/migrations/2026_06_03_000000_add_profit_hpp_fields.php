<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->decimal('cost_price', 12, 2)->default(0)->after('price');
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->decimal('capital_price', 12, 2)->default(0)->after('price');
            $table->decimal('profit', 12, 2)->default(0)->after('capital_price');
        });
    }

    public function down()
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('cost_price');
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn(['capital_price', 'profit']);
        });
    }
};
