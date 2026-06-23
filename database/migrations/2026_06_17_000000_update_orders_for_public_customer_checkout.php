<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('user_id');
            }

            if (! Schema::hasColumn('orders', 'notes')) {
                $table->text('notes')->nullable()->after('table_number');
            }

            if (! Schema::hasColumn('orders', 'payment_method')) {
                $table->string('payment_method', 50)->nullable()->after('total_price');
            }

            if (! Schema::hasColumn('orders', 'payment_status')) {
                $table->string('payment_status', 50)->default('pending')->after('payment_method');
            }
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `orders` MODIFY `table_number` VARCHAR(50) NULL");
            DB::statement("ALTER TABLE `orders` MODIFY `status` ENUM('waiting','pending','paid','processing','completed') NOT NULL DEFAULT 'waiting'");
        }
    }

    public function down()
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `orders` MODIFY `status` ENUM('pending','paid','processing','completed') NOT NULL DEFAULT 'pending'");
            DB::statement("ALTER TABLE `orders` MODIFY `table_number` INT NULL");
        }

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'payment_status')) {
                $table->dropColumn('payment_status');
            }

            if (Schema::hasColumn('orders', 'payment_method')) {
                $table->dropColumn('payment_method');
            }

            if (Schema::hasColumn('orders', 'notes')) {
                $table->dropColumn('notes');
            }

            if (Schema::hasColumn('orders', 'customer_name')) {
                $table->dropColumn('customer_name');
            }
        });
    }
};
