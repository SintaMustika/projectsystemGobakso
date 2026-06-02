<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up()
    {
        // Modify the enum to include processing and completed. Use raw statement to avoid requiring doctrine/dbal.
        DB::statement("ALTER TABLE `orders` MODIFY `status` ENUM('pending','paid','processing','completed') NOT NULL DEFAULT 'pending'");
    }

    public function down()
    {
        // Revert to original enum values (keep existing rows intact, values outside enum will error on revert)
        DB::statement("ALTER TABLE `orders` MODIFY `status` ENUM('pending','paid') NOT NULL DEFAULT 'pending'");
    }
};
