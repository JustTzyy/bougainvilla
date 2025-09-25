<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Models\Rate;
use App\Models\History;
use App\Models\Room;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class AccommodationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $accommodations = Accommodation::query()->orderBy('id')->paginate(10);
            return view('adminPages.accommodation', compact('accommodations'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to load accommodations: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'capacity' => 'required|integer|min:1',
                'description' => 'nullable|string|max:500',
            ]);

            $accommodation = Accommodation::create([
                'name' => $request->name,
                'capacity' => $request->capacity,
                'description' => $request->description,
            ]);

            History::create([
                'userID' => Auth::user()->id,
                'status' => 'Stored Accommodation: ' . $accommodation->name,
            ]);

            return redirect()->back()->with('success', 'Accommodation added successfully!');
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
                'name' => 'required|string|max:255',
                'capacity' => 'required|integer|min:1',
                'description' => 'nullable|string|max:500',
            ]);

            $accommodation = Accommodation::findOrFail($id);
            $accommodation->update([
                'name' => $request->name,
                'capacity' => $request->capacity,
                'description' => $request->description,
            ]);

            History::create([
                'userID' => Auth::user()->id,
                'status' => 'Updated Accommodation: ' . $accommodation->name,
            ]);

            return redirect()->back()->with('success', 'Accommodation updated successfully!');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $accommodation = Accommodation::findOrFail($id);

            // Soft delete related rates first and log each deletion (via pivot)
            $rates = Rate::whereHas('accommodations', function($q) use ($accommodation) {
                    $q->where('accommodations.id', $accommodation->id);
                })->get();
            foreach ($rates as $rate) {
                $rate->delete();
                History::create([
                    'userID' => Auth::user()->id,
                    'status' => 'Deleted Rate: ' . $rate->duration . ' - ₱' . number_format($rate->price, 2) . ' (Accommodation: ' . $accommodation->name . ')',
                ]);
            }

            // Log accommodation deletion
            History::create([
                'userID' => Auth::user()->id,
                'status' => 'Deleted Accommodation: ' . $accommodation->name,
            ]);

            // Then delete the accommodation
            $accommodation->delete();

            return redirect()->back()->with('success', 'Accommodation and related rates deleted successfully!');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            $accommodation = Accommodation::onlyTrashed()->findOrFail($id);
            $accommodation->restore();

            // Restore related rates too and log each restoration (via pivot)
            $rates = Rate::onlyTrashed()->whereHas('accommodations', function($q) use ($accommodation) {
                    $q->where('accommodations.id', $accommodation->id);
                })->get();
            foreach ($rates as $rate) {
                $rate->restore();
                History::create([
                    'userID' => Auth::user()->id,
                    'status' => 'Restored Rate: ' . $rate->duration . ' - ₱' . number_format($rate->price, 2) . ' (Accommodation: ' . $accommodation->name . ')',
                ]);
            }

            // Log accommodation restoration
            History::create([
                'userID' => Auth::user()->id,
                'status' => 'Restored Accommodation: ' . $accommodation->name,
            ]);

            return redirect()->back()->with('success', 'Accommodation and related rates restored successfully!');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function archived(Request $request)
    {
        try {
            $accommodations = Accommodation::onlyTrashed()->orderByDesc('deleted_at')->paginate(10);
            return view('adminPages.archiveaccommodations', compact('accommodations'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to load archived accommodations: ' . $e->getMessage());
        }
    }

    public function getRooms($id)
    {
        try {
            $accommodation = Accommodation::findOrFail($id);
            $rooms = Room::whereHas('accommodations', function($query) use ($id) {
                $query->where('accommodation_id', $id);
            })->with('level')->get();
            
            $rooms = $rooms->map(function($room) {
                return [
                    'id' => $room->id,
                    'room' => $room->room,
                    'status' => $room->status,
                    'type' => $room->type,
                    'level' => $room->level ? $room->level->id : 'N/A'
                ];
            });
            
            return response()->json(['rooms' => $rooms]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to load rooms'], 500);
        }
    }

    public function getRates($id)
    {
        try {
            $accommodation = Accommodation::findOrFail($id);
            $rates = Rate::whereHas('accommodations', function($q) use ($id) {
                        $q->where('accommodations.id', $id);
                    })->get();
            
            $rates = $rates->map(function($rate) {
                return [
                    'id' => $rate->id,
                    'duration' => $rate->duration,
                    'price' => $rate->price,
                    'status' => $rate->status
                ];
            });
            
            return response()->json(['rates' => $rates]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to load rates'], 500);
        }
    }
}
