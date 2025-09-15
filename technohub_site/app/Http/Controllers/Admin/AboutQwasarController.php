<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AboutQwasarRequest;
use App\Models\AboutQwasar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AboutQwasarController extends Controller
{
    public function index(){
        $aboutQwasar = AboutQwasar::first();
        return view('pages.admin.aboutQwasar.index', compact('aboutQwasar'));
    }

    public function store(AboutQwasarRequest $request){
        try {
            $aboutQwasar = AboutQwasar::create($request->validated());
            if($request->hasFile('video')){
                $aboutQwasar->video = $request->file('video')->store('aboutQwasar', 'public');
            }
            $aboutQwasar->save();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while updating the page.');
        }
        return redirect()->route('aboutQwasar')->with('success', 'Page updated successfully');
    }

    public function getById($id)
    {
        try {
            $aboutQwasar = AboutQwasar::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $aboutQwasar
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(AboutQwasarRequest $request)
    {
        try {
            $aboutQwasar = AboutQwasar::findOrFail($request->id);

            $aboutQwasar->update($request->validated());

            if($request->hasFile('video')){
                if($aboutQwasar->video){
                    Storage::disk('public')->delete($aboutQwasar->video);
                }
                $aboutQwasar->video = $request->file('video')->store('aboutQwasar', 'public');
            }

            $aboutQwasar->save();

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
            $aboutQwasar = AboutQwasar::findOrFail($request->id);
            if($aboutQwasar->video){
                Storage::disk('public')->delete($aboutQwasar->video);
            }
            $aboutQwasar->delete();

            return redirect()->back()->with('success', 'Record deleted successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Record not found.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
