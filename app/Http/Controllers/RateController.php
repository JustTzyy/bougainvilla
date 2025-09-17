<?php

namespace App\Http\Controllers;

use App\Models\Rate;
use App\Models\Accommodation;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\QueryException;

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

            Rate::create($request->all());

            return back()->with('success', 'Rate added successfully!');
        } catch (QueryException $e) {
            return back()->with('error', 'Database error: ' . $e->getMessage());
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

            return back()->with('success', 'Rate updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $rate = Rate::findOrFail($id);
            $rate->delete();

            return back()->with('success', 'Rate deleted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            $rate = Rate::onlyTrashed()->findOrFail($id);
            $rate->restore();

            return back()->with('success', 'Rate restored successfully!');
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
