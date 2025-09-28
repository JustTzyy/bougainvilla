<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

class CleanupController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $threeMonthsAgo = $now->copy()->subMonths(3);
        $sixMonthsAgo = $now->copy()->subMonths(6);

        // Get statistics
        $totalGuests = Guest::count();
        $activeGuests = Guest::whereNull('deleted_at')->count();
        $softDeletedGuests = Guest::onlyTrashed()->count();

        // Guests ready for soft delete (3+ months old)
        $guestsForSoftDelete = Guest::whereNull('deleted_at')
            ->where('created_at', '<=', $threeMonthsAgo)
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        // Guests ready for hard delete (soft deleted 3+ months ago)
        $guestsForHardDelete = Guest::onlyTrashed()
            ->where('deleted_at', '<=', $threeMonthsAgo)
            ->where('created_at', '<=', $sixMonthsAgo)
            ->orderBy('deleted_at', 'asc')
            ->paginate(10);

        return view('adminPages.cleanup', compact(
            'totalGuests',
            'activeGuests',
            'softDeletedGuests',
            'guestsForSoftDelete',
            'guestsForHardDelete',
            'threeMonthsAgo',
            'sixMonthsAgo'
        ));
    }

    public function runCleanup(Request $request)
    {
        $dryRun = $request->boolean('dry_run', false);
        
        try {
            $command = $dryRun ? 'guests:cleanup --dry-run' : 'guests:cleanup';
            $exitCode = Artisan::call($command);
            $output = Artisan::output();

            return response()->json([
                'success' => true,
                'output' => $output,
                'exit_code' => $exitCode
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error running cleanup: ' . $e->getMessage()
            ], 500);
        }
    }

    public function forceSoftDelete($id)
    {
        try {
            $guest = Guest::findOrFail($id);
            $guest->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Guest soft deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error soft deleting guest: ' . $e->getMessage()
            ], 500);
        }
    }

    public function forceHardDelete($id)
    {
        try {
            $guest = Guest::onlyTrashed()->findOrFail($id);
            $guest->forceDelete();
            
            return response()->json([
                'success' => true,
                'message' => 'Guest permanently deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error permanently deleting guest: ' . $e->getMessage()
            ], 500);
        }
    }
}
