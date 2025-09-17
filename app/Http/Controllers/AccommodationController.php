<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Models\Rate;
use Exception;
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

            Accommodation::create([
                'name' => $request->name,
                'capacity' => $request->capacity,
                'description' => $request->description,
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

            // Soft delete related rates first
            Rate::where('accommodation_id', $accommodation->id)->delete();

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

            // Restore related rates too
            Rate::onlyTrashed()
                ->where('accommodation_id', $accommodation->id)
                ->restore();

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
}
