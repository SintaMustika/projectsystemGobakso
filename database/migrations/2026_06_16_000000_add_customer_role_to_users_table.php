<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up()
    {
        // Try MySQL/MariaDB first
        try {
            $driver = DB::getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME);
        } catch (\Throwable $e) {
            $driver = null;
        }

        if ($driver === 'mysql' || $driver === 'mariadb') {
            // Modify enum to include customer; keep default as 'kasir'
            DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin','kasir','dapur','owner','customer') NOT NULL DEFAULT 'kasir'");
            return;
        }

        if ($driver === 'pgsql') {
            // For PostgreSQL, add value to enum type if exists; otherwise attempt to recreate type safely
            try {
                // Try to add value 'customer' to existing enum type used by users.role
                DB::statement("DO $$\nBEGIN\n  IF NOT EXISTS (SELECT 1 FROM pg_type t JOIN pg_enum e ON t.oid = e.enumtypid WHERE t.typname = 'user_role' AND e.enumlabel = 'customer') THEN\n    BEGIN\n      ALTER TYPE user_role ADD VALUE 'customer';\n    EXCEPTION WHEN others THEN NULL; END;\n  END IF;\nEND$$;");
            } catch (\Throwable $e) {
                logger()->warning('Could not add customer to postgres enum: ' . $e->getMessage());
            }
            return;
        }

        // Fallback for SQLite or others: attempt schema change (requires doctrine/dbal)
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['admin','kasir','dapur','owner','customer'])->default('kasir')->change();
            });
        } catch (\Throwable $e) {
            logger()->warning('Could not update users.role enum via schema change: ' . $e->getMessage());
        }
    }

    public function down()
    {
        try {
            $driver = DB::getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME);
        } catch (\Throwable $e) {
            $driver = null;
        }

        if ($driver === 'mysql' || $driver === 'mariadb') {
            // Before reverting, convert any 'customer' values to default to avoid truncation
            DB::table('users')->where('role', 'customer')->update(['role' => 'kasir']);
            DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin','kasir','dapur','owner') NOT NULL DEFAULT 'kasir'");
            return;
        }

        if ($driver === 'pgsql') {
            try {
                // No portable safe way to remove enum labels if they are used; leave as-is but log
                logger()->warning('Down migration for postgres enum removal not supported automatically.');
            } catch (\Throwable $e) {
                logger()->warning('Error in pg down migration: ' . $e->getMessage());
            }
            return;
        }

        try {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['admin','kasir','dapur','owner'])->default('kasir')->change();
            });
        } catch (\Throwable $e) {
            logger()->warning('Could not revert users.role enum via schema change: ' . $e->getMessage());
        }
    }
};
