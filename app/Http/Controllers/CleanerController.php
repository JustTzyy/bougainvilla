<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Stay;
use App\Models\Room;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\History;

class CleanerController extends Controller
{
    public function dashboard()
    {
        try {
            $cleanerId = Auth::id();
            
            // Get rooms assigned to this cleaner that need cleaning
            $roomsToClean = Stay::with(['room', 'rate', 'guests'])
                ->where('assigned_cleaner_id', $cleanerId)
                ->where('status', Stay::STATUS_CLEANING)
                ->orderBy('checkOut', 'desc')
                ->get();

            // Get recent cleaning history
            $recentCleanings = Stay::with(['room', 'rate'])
                ->where('assigned_cleaner_id', $cleanerId)
                ->where('status', Stay::STATUS_READY)
                ->orderBy('updated_at', 'desc')
                ->limit(10)
                ->get();

            // Get statistics
            $stats = [
                'rooms_to_clean' => $roomsToClean->count(),
                'rooms_cleaned_today' => Stay::where('assigned_cleaner_id', $cleanerId)
                    ->where('status', Stay::STATUS_READY)
                    ->whereDate('updated_at', today())
                    ->count(),
                'total_penalties_reported' => Stay::where('assigned_cleaner_id', $cleanerId)
                    ->whereNotNull('penalty_amount')
                    ->where('penalty_amount', '>', 0)
                    ->count(),
                'total_penalty_amount' => Stay::where('assigned_cleaner_id', $cleanerId)
                    ->whereNotNull('penalty_amount')
                    ->sum('penalty_amount')
            ];

            return view('cleaner.dashboard', compact('roomsToClean', 'recentCleanings', 'stats'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load dashboard: ' . $e->getMessage());
        }
    }

    public function rooms()
    {
        try {
            $cleanerId = Auth::id();
            
            // Get all rooms assigned to this cleaner
            $rooms = Stay::with(['room', 'rate', 'guests'])
                ->where('assigned_cleaner_id', $cleanerId)
                ->whereIn('status', [Stay::STATUS_CLEANING, Stay::STATUS_READY])
                ->orderBy('checkOut', 'desc')
                ->get();

            return view('cleaner.rooms', compact('rooms'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load rooms: ' . $e->getMessage());
        }
    }

    public function markRoomReady(Request $request, $stayId)
    {
        try {
            $cleanerId = Auth::id();
            
            $stay = Stay::where('id', $stayId)
                ->where('assigned_cleaner_id', $cleanerId)
                ->where('status', Stay::STATUS_CLEANING)
                ->firstOrFail();

            DB::beginTransaction();

            // Update stay status to Ready
            $stay->update(['status' => Stay::STATUS_READY]);

            // Update room status to Available
            $room = Room::find($stay->roomID);
            if ($room) {
                $room->update(['status' => 'Available']);
            }

            // Add history
            History::create([
                'status' => 'Room marked as ready by cleaner',
                'userID' => $cleanerId,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Room marked as ready successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark room as ready: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reportDamage(Request $request, $stayId)
    {
        try {
            $request->validate([
                'penalty_amount' => 'required|numeric|min:0|max:999999.99',
                'penalty_reason' => 'required|string|max:1000'
            ]);

            $cleanerId = Auth::id();
            
            $stay = Stay::where('id', $stayId)
                ->where('assigned_cleaner_id', $cleanerId)
                ->firstOrFail();

            DB::beginTransaction();

            // Update stay with penalty information
            $stay->update([
                'penalty_amount' => $request->penalty_amount,
                'penalty_reason' => $request->penalty_reason
            ]);

            // Update payment if it exists
            $payment = Payment::where('stayID', $stay->id)->first();
            if ($payment) {
                $newTotal = $payment->subtotal + $request->penalty_amount;
                $payment->update(['amount' => $newTotal]);
            }

            // Add history
            History::create([
                'status' => 'Damage reported by cleaner',
                'userID' => $cleanerId,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Damage report submitted successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit damage report: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reports()
    {
        try {
            $cleanerId = Auth::id();
            
            // Get all damage reports by this cleaner
            $reports = Stay::with(['room', 'rate', 'guests'])
                ->where('assigned_cleaner_id', $cleanerId)
                ->whereNotNull('penalty_amount')
                ->where('penalty_amount', '>', 0)
                ->orderBy('updated_at', 'desc')
                ->get();

            return view('cleaner.reports', compact('reports'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load reports: ' . $e->getMessage());
        }
    }
}