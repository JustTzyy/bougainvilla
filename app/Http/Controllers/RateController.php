<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Models\Rate;
use App\Models\Accommodation;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

class RateController extends Controller
{
    public function index()
    {
        try {
            $rates = Rate::with('accommodation')->orderBy('id')->paginate(10);
            $accommodations = Accommodation::all();
            return view('adminPages.rate', compact('rates', 'accommodations'));
        } catch (Exception $e) {
            return back()->with('error', 'Failed to load rates: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'duration' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'accommodation_id' => 'required|exists:accommodations,id',
            ]);

            $rate = Rate::create($request->all());

            // Log history
            History::create([
                'userID' => Auth::user()->id,
                'status' => 'Stored Rate: ' . $rate->duration . ' - ' . $rate->price,
            ]);

            return back()->with('success', 'Rate added successfully!');
        } catch (QueryException $e) {
            return back()->with('error', 'Database error: ' . $e->getMessage());
        } catch (Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'duration' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'status' => 'required|in:Active,Inactive',
                'accommodation_id' => 'required|exists:accommodations,id',
            ]);

            $rate = Rate::findOrFail($id);
            $rate->update($request->all());

            // Log history
            History::create([
                'userID' => Auth::user()->id,
                'status' => 'Updated Rate: ' . $rate->duration . ' - ' . $rate->price,
            ]);

            return back()->with('success', 'Rate updated successfully!');
        } catch (QueryException $e) {
            return back()->with('error', 'Database error: ' . $e->getMessage());
        } catch (Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $rate = Rate::findOrFail($id);

            // Log history before deletion
            History::create([
                'userID' => Auth::user()->id,
                'status' => 'Deleted Rate: ' . $rate->duration . ' - ' . $rate->price,
            ]);

            $rate->delete();

            return back()->with('success', 'Rate deleted successfully!');
        } catch (QueryException $e) {
            return back()->with('error', 'Database error: ' . $e->getMessage());
        } catch (Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

   public function restore($id)
{
    try {
        // Find the trashed rate
        $rate = Rate::onlyTrashed()->findOrFail($id);

        // Check if the associated accommodation is trashed and restore it
        $accommodation = Accommodation::withTrashed()->find($rate->accommodation_id);
        if ($accommodation && $accommodation->trashed()) {
            $accommodation->restore();

            // Log history for accommodation restore
            History::create([
                'userID' => Auth::user()->id,
                'status' => 'Restored Accommodation: ' . $accommodation->name,
            ]);
        }

        // Restore the rate
        $rate->restore();

        // Log history for rate restore
        History::create([
            'userID' => Auth::user()->id,
            'status' => 'Restored Rate: ' . $rate->duration . ' - ' . $rate->price,
        ]);

        return back()->with('success', 'Rate and its accommodation restored successfully!');
    } catch (QueryException $e) {
        return back()->with('error', 'Database error: ' . $e->getMessage());
    } catch (Exception $e) {
        return back()->with('error', 'Something went wrong: ' . $e->getMessage());
    }
}
    public function archived()
    {
        try {
            $rates = Rate::onlyTrashed()->with('accommodation')->paginate(10);
            return view('adminPages.archiveRates', compact('rates'));
        } catch (Exception $e) {
            return back()->with('error', 'Failed to load archived rates: ' . $e->getMessage());
        }
    }
}
