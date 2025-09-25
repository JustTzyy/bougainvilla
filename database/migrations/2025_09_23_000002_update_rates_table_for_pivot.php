<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Backfill pivot from existing rates.accommodation_id, then drop the column
        if (Schema::hasColumn('rates', 'accommodation_id')) {
            // Create pivot rows for existing data
            $rates = DB::table('rates')->select('id', 'accommodation_id')->whereNotNull('accommodation_id')->get();
            foreach ($rates as $rate) {
                // Ensure no duplicates
                $exists = DB::table('rate_accommodations')
                    ->where('rate_id', $rate->id)
                    ->where('accommodation_id', $rate->accommodation_id)
                    ->exists();
                if (!$exists) {
                    DB::table('rate_accommodations')->insert([
                        'rate_id' => $rate->id,
                        'accommodation_id' => $rate->accommodation_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            Schema::table('rates', function (Blueprint $table) {
                // Drop FK and column safely
                try {
                    $table->dropForeign(['accommodation_id']);
                } catch (\Throwable $e) {
                    // ignore if not present
                }
                $table->dropColumn('accommodation_id');
            });
        }
    }

    public function down(): void
    {
        // Re-add the column for rollback (no data restoration)
        Schema::table('rates', function (Blueprint $table) {
            if (!Schema::hasColumn('rates', 'accommodation_id')) {
                $table->foreignId('accommodation_id')->nullable()->constrained('accommodations')->onDelete('cascade');
            }
        });
    }
};






