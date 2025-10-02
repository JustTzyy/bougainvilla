<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Level;
use App\Models\Accommodation;
use App\Models\RoomAccommodation;
use App\Models\History;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoomController extends Controller
{
    use SafeDataAccessTrait;
    use EnhancedLoggingTrait;
    public function index(Request $request)
    {
        try {
            $rooms = Room::with(['level', 'accommodations'])
                        ->orderBy('room')
                        ->get();
            
            $levels = Level::where('status', 'Active')->get();
            $accommodations = Accommodation::all();
            
            return view('adminPages.room', compact('rooms', 'levels', 'accommodations'));
        } catch (Exception $e) {
            \Log::error('Room Controller Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Failed to load rooms: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'room' => 'required|string|max:255|unique:rooms,room',
                'status' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'level_id' => 'required|exists:levels,id',
                'accommodations' => 'array',
                'accommodations.*' => 'exists:accommodations,id',
            ]);

            DB::beginTransaction();

            $room = Room::create([
                'room' => $request->room,
                'status' => $request->status,
                'type' => $request->type,
                'level_id' => $request->level_id,
            ]);

            // Attach accommodations if provided
            if ($request->has('accommodations') && is_array($request->accommodations)) {
                $room->accommodations()->attach($request->accommodations);
            }

            // Log history
            History::create([
                'userID' => Auth::user()->id,
                'status' => 'Created room: ' . $room->room,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Room added successfully!');
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'room' => 'required|string|max:255|unique:rooms,room,' . $id,
                'status' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'level_id' => 'required|exists:levels,id',
                'accommodations' => 'array',
                'accommodations.*' => 'exists:accommodations,id',
            ]);

            DB::beginTransaction();

            $room = Room::findOrFail($id);
            $oldStatus = $room->status;
            $newStatus = $request->status;
            
            $room->update([
                'room' => $request->room,
                'status' => $newStatus,
                'type' => $request->type,
                'level_id' => $request->level_id,
            ]);

            // Sync accommodations
            if ($request->has('accommodations')) {
                $room->accommodations()->sync($request->accommodations);
            } else {
                $room->accommodations()->detach();
            }

            // Handle automatic archiving/restoration based on status
            if ($oldStatus !== $newStatus) {
                if ($newStatus === 'Under Maintenance' && $oldStatus !== 'Under Maintenance') {
                    // Archive the room (soft delete)
                    $room->delete();
                    
                    History::create([
                        'userID' => Auth::user()->id,
                        'status' => 'Archived room: ' . $room->room . ' (Status changed to Under Maintenance)',
                    ]);
                    
                    DB::commit();
                    return redirect()->back()->with('success', 'Room updated and archived due to maintenance status!');
                } elseif ($oldStatus === 'Under Maintenance' && $newStatus === 'Available') {
                    // Restore the room if it was previously archived
                    $room->restore();
                    
                    History::create([
                        'userID' => Auth::user()->id,
                        'status' => 'Restored room: ' . $room->room . ' (Status changed to Available)',
                    ]);
                }
            }

            // Log history
            History::create([
                'userID' => Auth::user()->id,
                'status' => 'Updated room: ' . $room->room,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Room updated successfully!');
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function archive(Request $request, $id)
    {
        try {
            $request->validate([
                'archive_reason' => 'required|string|max:500',
            ]);

            $room = Room::findOrFail($id);
            $roomNumber = $room->room;
            $archiveReason = $request->archive_reason;
            
            // Update room status to the archive reason
            $room->update([
                'status' => $archiveReason,
            ]);
            
            // Archive the room (soft delete)
            $room->delete();
            
            History::create([
                'userID' => Auth::user()->id,
                'status' => 'Archived room: ' . $roomNumber . ' - Reason: ' . $archiveReason,
            ]);
            
            return redirect()->back()->with('success', 'Room archived successfully!');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $room = Room::findOrFail($id);
            $roomNumber = $room->room;
            
            // Perform soft delete
            $room->delete();
            
            History::create([
                'userID' => Auth::user()->id,
                'status' => 'Deleted room: ' . $roomNumber,
            ]);
            
            return redirect()->back()->with('success', 'Room deleted successfully!');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function restore(Request $request, $id)
    {
        try {
            $room = Room::onlyTrashed()->findOrFail($id);
            $room->restore();

            // Update status to Available if provided
            if ($request->has('status') && $request->status === 'Available') {
                $room->update(['status' => 'Available']);
            }

            History::create([
                'userID' => Auth::user()->id,
                'status' => 'Restored room: ' . $room->room . ' (Status: Available)',
            ]);

            return redirect()->back()->with('success', 'Room restored successfully with Available status!');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function archived(Request $request)
    {
        try {
            $rooms = Room::onlyTrashed()
                        ->with(['level', 'accommodations'])
                        ->orderByDesc('deleted_at')
                        ->get();
            
            return view('adminPages.archiverooms', compact('rooms'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to load archived rooms: ' . $e->getMessage());
        }
    }

    public function getRoomDetails($id)
    {
        try {
            $room = Room::with(['level', 'accommodations'])->findOrFail($id);
            return response()->json($room);
        } catch (Exception $e) {
            return response()->json(['error' => 'Room not found'], 404);
        }
    }

    public function getAccommodations($id)
    {
        try {
            $room = Room::findOrFail($id);
            $accommodations = $room->accommodations;
            
            $accommodations = $accommodations->map(function($accommodation) {
                return [
                    'id' => $accommodation->id,
                    'name' => $accommodation->name,
                    'capacity' => $accommodation->capacity,
                    'description' => $accommodation->description
                ];
            });
            
            return response()->json(['accommodations' => $accommodations]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to load accommodations'], 500);
        }
    }
}
