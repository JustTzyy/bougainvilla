<?php

namespace App\Http\Controllers;

use App\Models\Level;
use App\Models\Room;
use App\Models\History;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    public function index(Request $request)
    {
        try {
            $levels = Level::query()->orderBy('id')->paginate(10);
            return view('adminPages.level', compact('levels'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to load levels: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'description' => 'required|string|max:255',
            ]);

            $level = Level::create([
                'description' => $request->description,
                'status' => 'Active',
            ]);

            History::create([
                'userID' => Auth::user()->id,
                'status' => 'Stored Level: ' . $level->description,
            ]);

            return redirect()->back()->with('success', 'Level added successfully!');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'description' => 'required|string|max:255',
                'status' => 'required|string|in:Active,Inactive',
            ]);

            $level = Level::findOrFail($id);
            $level->update([
                'description' => $request->description,
                'status' => $request->status,
            ]);

            History::create([
                'userID' => Auth::user()->id,
                'status' => 'Updated Level: ' . $level->description,
            ]);

            return redirect()->back()->with('success', 'Level updated successfully!');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:255',
            ]);

            $level = Level::findOrFail($id);

            // Update status with reason before deletion
            $level->status = $request->reason;
            $level->save();

            // Delete associated rooms (soft delete)
            foreach ($level->rooms as $room) {
                $room->delete();
                History::create([
                    'userID' => Auth::user()->id,
                    'status' => 'Deleted Room: ' . $room->name . ' (Level: ' . $level->description . ')',
                ]);
            }

            History::create([
                'userID' => Auth::user()->id,
                'status' => 'Deleted Level: ' . $level->description,
            ]);

            $level->delete();

            return redirect()->back()->with('success', 'Level archived successfully!');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            $level = Level::onlyTrashed()->findOrFail($id);

            // Restore associated rooms if trashed
            foreach ($level->rooms()->withTrashed()->get() as $room) {
                if ($room->trashed()) {
                    $room->restore();
                    History::create([
                        'userID' => Auth::user()->id,
                        'status' => 'Restored Room: ' . $room->name . ' (Level: ' . $level->description . ')',
                    ]);
                }
            }

            // Restore the level
            $level->restore();
            $level->status = 'Active';
            $level->save();

            History::create([
                'userID' => Auth::user()->id,
                'status' => 'Restored Level: ' . $level->description,
            ]);

            return redirect()->back()->with('success', 'Level and its rooms restored successfully!');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function archived(Request $request)
    {
        try {
            $levels = Level::onlyTrashed()->orderByDesc('deleted_at')->paginate(10);
            return view('adminPages.archivelevels', compact('levels'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to load archived levels: ' . $e->getMessage());
        }
    }

    public function getRooms($id)
    {
        try {
            $level = Level::with(['rooms.accommodations'])->findOrFail($id);
            $rooms = $level->rooms->map(function($room) {
                return [
                    'id' => $room->id,
                    'name' => $room->room, // Fixed: using 'room' field instead of 'name'
                    'status' => $room->status,
                    'type' => $room->type,
                    'accommodations' => $room->accommodations->map(function($accommodation) {
                        return [
                            'id' => $accommodation->id,
                            'name' => $accommodation->name
                        ];
                    })
                ];
            });
            
            return response()->json(['rooms' => $rooms]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to load rooms'], 500);
        }
    }
}
