<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Widen to VARCHAR to avoid ENUM coercion errors during data fixes
        DB::statement("ALTER TABLE `rates` MODIFY COLUMN `status` VARCHAR(50) NULL");

        // 2) Normalize existing values to the new domain
        DB::statement("UPDATE `rates` SET `status` = 'Standard' WHERE `status` IS NULL OR `status` NOT IN ('Standard','Extending','Extending/Standard')");

        // 3) Constrain to the new ENUM set with default
        DB::statement("ALTER TABLE `rates` MODIFY COLUMN `status` ENUM('Standard','Extending','Extending/Standard') NOT NULL DEFAULT 'Standard'");
    }

    public function down(): void
    {
        // Attempt to revert to a permissive VARCHAR if the old enum set is unknown
        DB::statement("ALTER TABLE `rates` MODIFY COLUMN `status` VARCHAR(50) NOT NULL");
    }
};


