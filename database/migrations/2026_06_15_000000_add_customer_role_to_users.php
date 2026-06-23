<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Determine PDO driver (mysql, pgsql, sqlite, etc.)
        try {
            $pdoDriver = DB::getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME);
        } catch (\Throwable $e) {
            $pdoDriver = null;
        }

        if ($pdoDriver === 'mysql' || $pdoDriver === 'mariadb') {
            DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin','kasir','owner','dapur','customer') NOT NULL DEFAULT 'kasir'");
        } else {
            // Fallback: attempt to alter using change() if doctrine/dbal available
            try {
                Schema::table('users', function ($table) {
                    $table->enum('role', ['admin','kasir','owner','dapur','customer'])->default('kasir')->change();
                });
            } catch (\Throwable $e) {
                // If cannot change, silently fail with a log entry
                logger()->warning('Could not update users.role enum to include customer: ' . $e->getMessage());
            }
        }
    }

    public function down()
    {
        try {
            $pdoDriver = DB::getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME);
        } catch (\Throwable $e) {
            $pdoDriver = null;
        }

        if ($pdoDriver === 'mysql' || $pdoDriver === 'mariadb') {
            DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin','kasir','owner','dapur') NOT NULL DEFAULT 'kasir'");
        } else {
            try {
                Schema::table('users', function ($table) {
                    $table->enum('role', ['admin','kasir','owner','dapur'])->default('kasir')->change();
                });
            } catch (\Throwable $e) {
                logger()->warning('Could not revert users.role enum: ' . $e->getMessage());
            }
        }
    }
};
