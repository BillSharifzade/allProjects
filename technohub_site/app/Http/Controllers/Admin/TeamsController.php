<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeamsReques;
use App\Models\Teams;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamsController extends Controller
{
    public function index(){
        $teams = Teams::orderBy('id','desc')->get();
        return view('pages.admin.teams.index', compact('teams'));
    }

    public function store(TeamsReques $request){
        try {
            $teams = Teams::create($request->validated());
            if ($request->hasFile('img')) {
                $teams->img = $request->file('img')->store('Team', 'public');
            }

            $teams->save();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while updating the page.');
        }
        return redirect()->back()->with('success', 'Page updated successfully');
    }

    public function getById($id)
    {
        try {
            $teams = Teams::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $teams
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(TeamsReques $request)
    {
        try {
            $teams = Teams::findOrFail($request->id);

            $teams->update($request->validated());

            if ($request->hasFile('img')) {
                if ($teams->img) {
                    Storage::disk('public')->delete($teams->img);
                }
                $teams->img = $request->file('img')->store('Team', 'public');
            }

            $teams->save();

            return redirect()->back()->with('success', 'Record updated successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Record not found.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function delete(Request $request)
    {
        try {
            $teams = Teams::findOrFail($request->id);

            if ($teams->img) {
                Storage::disk('public')->delete($teams->img);
            }

            $teams->delete();

            return redirect()->back()->with('success', 'Record deleted successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Record not found.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function changeStatus($id, $status){
        try {
            $teams = Teams::findOrFail($id);
            $teams->update(['status' => $status]);
            $teams->save();

            return response()->json([
                'success' => true,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Record not found.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
