<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Adjust enum to add 'dapur' value — MySQL raw statement
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin','kasir','owner','dapur') NOT NULL DEFAULT 'kasir'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // revert to previous enum (without 'dapur')
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin','kasir','owner') NOT NULL DEFAULT 'kasir'");
    }
};
