<?php

namespace App\Http\Controllers;

use App\Models\Level;
use App\Models\History;
use Exception;
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

            return redirect()->back()->with('success', 'Level updated successfully!');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $level = Level::findOrFail($id);
            $level->delete();



            return redirect()->back()->with('success', 'Level deleted successfully!');
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
            $level->restore();



            return redirect()->back()->with('success', 'Level restored successfully!');
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
}


