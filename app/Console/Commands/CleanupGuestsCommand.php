<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Guest;
use Carbon\Carbon;

class CleanupGuestsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guests:cleanup {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup guests: soft delete after 3 months, hard delete after 6 months total';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $now = Carbon::now();
        
        // Step 1: Soft delete guests that are 3 months old and not already soft deleted
        $threeMonthsAgo = $now->copy()->subMonths(3);
        $activeGuestsToSoftDelete = Guest::whereNull('deleted_at')
            ->where('created_at', '<=', $threeMonthsAgo)
            ->get();

        if ($activeGuestsToSoftDelete->count() > 0) {
            $this->info("Found {$activeGuestsToSoftDelete->count()} active guests to soft delete (older than 3 months)");
            
            if ($isDryRun) {
                $this->table(
                    ['ID', 'Name', 'Created At', 'Days Old'],
                    $activeGuestsToSoftDelete->map(function ($guest) use ($now) {
                        return [
                            $guest->id,
                            $guest->firstName . ' ' . $guest->lastName,
                            $guest->created_at->format('Y-m-d H:i:s'),
                            round($guest->created_at->diffInDays($now))
                        ];
                    })
                );
            } else {
                foreach ($activeGuestsToSoftDelete as $guest) {
                    $guest->delete(); // This will soft delete
                }
                $this->info("Soft deleted {$activeGuestsToSoftDelete->count()} guests");
            }
        } else {
            $this->info("No active guests found older than 3 months");
        }

        // Step 2: Hard delete guests that have been soft deleted for 3+ months (6 months total)
        $sixMonthsAgo = $now->copy()->subMonths(6);
        $softDeletedGuestsToHardDelete = Guest::onlyTrashed()
            ->where('deleted_at', '<=', $threeMonthsAgo) // Soft deleted 3+ months ago
            ->where('created_at', '<=', $sixMonthsAgo) // Originally created 6+ months ago
            ->get();

        if ($softDeletedGuestsToHardDelete->count() > 0) {
            $this->info("Found {$softDeletedGuestsToHardDelete->count()} soft-deleted guests to permanently delete (soft deleted 3+ months ago)");
            
            if ($isDryRun) {
                $this->table(
                    ['ID', 'Name', 'Created At', 'Soft Deleted At', 'Days Since Soft Delete'],
                    $softDeletedGuestsToHardDelete->map(function ($guest) use ($now) {
                        return [
                            $guest->id,
                            $guest->firstName . ' ' . $guest->lastName,
                            $guest->created_at->format('Y-m-d H:i:s'),
                            $guest->deleted_at->format('Y-m-d H:i:s'),
                            round($guest->deleted_at->diffInDays($now))
                        ];
                    })
                );
            } else {
                foreach ($softDeletedGuestsToHardDelete as $guest) {
                    $guest->forceDelete(); // This will permanently delete
                }
                $this->info("Permanently deleted {$softDeletedGuestsToHardDelete->count()} guests");
            }
        } else {
            $this->info("No soft-deleted guests found ready for permanent deletion");
        }

        // Summary
        $totalProcessed = $activeGuestsToSoftDelete->count() + $softDeletedGuestsToHardDelete->count();
        
        if ($isDryRun) {
            $this->warn("DRY RUN: Would process {$totalProcessed} guests total");
        } else {
            $this->info("Cleanup completed: Processed {$totalProcessed} guests total");
        }

        return 0;
    }
}
