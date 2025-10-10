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
        Schema::table('stays', function (Blueprint $table) {
            $table->unsignedBigInteger('assigned_cleaner_id')->nullable()->after('penalty_reason');
            $table->foreign('assigned_cleaner_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stays', function (Blueprint $table) {
            $table->dropForeign(['assigned_cleaner_id']);
            $table->dropColumn('assigned_cleaner_id');
        });
    }
};
