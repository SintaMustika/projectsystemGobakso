<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change precision/scale of stock and qty fields from scale 4 -> 3
        // Use raw statements to avoid requiring doctrine/dbal
        DB::statement("ALTER TABLE `ingredients` MODIFY `stock_quantity` DECIMAL(12,3) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE `ingredients` MODIFY `min_stock` DECIMAL(12,3) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE `recipes` MODIFY `qty_usage` DECIMAL(12,3) NOT NULL DEFAULT 0");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `ingredients` MODIFY `stock_quantity` DECIMAL(12,4) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE `ingredients` MODIFY `min_stock` DECIMAL(12,4) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE `recipes` MODIFY `qty_usage` DECIMAL(12,4) NOT NULL DEFAULT 0");
    }
};
