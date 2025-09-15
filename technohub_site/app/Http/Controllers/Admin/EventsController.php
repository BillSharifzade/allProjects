<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EventsRequest;
use App\Models\Events;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventsController extends Controller
{
    public function index(){
        $events = Events::orderBy('id','desc')->get();
        return view('pages.admin.events.index', compact('events'));
    }

    public function store(EventsRequest $request){
        try {
            $events = Events::create($request->validated());
            if ($request->hasFile('img')) {
                $events->img = $request->file('img')->store('Events', 'public');
            }

            $events->save();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while updating the page.');
        }
        return redirect()->back()->with('success', 'Page updated successfully');
    }

    public function getById($id)
    {
        try {
            $events = Events::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $events
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(EventsRequest $request)
    {
        try {
            $events = Events::findOrFail($request->id);

            $events->update($request->validated());

            if ($request->hasFile('img')) {
                if ($events->img) {
                    Storage::disk('public')->delete($events->img);
                }
                $events->img = $request->file('img')->store('Events', 'public');
            }

            $events->save();

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
            $events = Events::findOrFail($request->id);

            if ($events->img) {
                Storage::disk('public')->delete($events->img);
            }

            $events->delete();

            return redirect()->back()->with('success', 'Record deleted successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Record not found.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function changeStatus($id, $status){
        try {
            $events = Events::findOrFail($id);
            $events->update(['status' => $status]);
            $events->save();

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
