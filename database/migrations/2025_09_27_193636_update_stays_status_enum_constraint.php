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
        // Update the status column to only allow 'Standard' and 'Extend' values
        DB::statement("ALTER TABLE stays MODIFY COLUMN status ENUM('Standard', 'Extend') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to the original enum values if needed
        DB::statement("ALTER TABLE stays MODIFY COLUMN status ENUM('Active', 'Standard', 'Extend', 'Completed') NOT NULL");
    }
};
