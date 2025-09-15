<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AboutUsRequest;
use App\Models\AboutUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AboutUsController extends Controller
{
    public function index(){
        $aboutUs = AboutUs::first();
        return view('pages.admin.aboutUs.index', compact('aboutUs'));
    }

    public function store(AboutUsRequest $request){
        try {
            $aboutUs = AboutUs::create($request->validated());
            $imageFields = ['img_1', 'img_2'];

            foreach ($imageFields as $field) {
                if ($request->hasFile($field)) {
                    $aboutUs->{$field} = $request->file($field)->store('aboutUs', 'public');
                }
            }
            $aboutUs->save();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while updating the page.');
        }
        return redirect()->route('aboutUs')->with('success', 'Page updated successfully');
    }

    public function getById($id)
    {
        try {
            $aboutUs = AboutUs::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $aboutUs
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(AboutUsRequest $request)
    {
        try {
            $aboutUs = AboutUs::findOrFail($request->id);

            $aboutUs->update($request->validated());

            $imageFields = ['img_1', 'img_2'];
            foreach ($imageFields as $field) {
                if ($request->hasFile($field)) {
                    if ($aboutUs->{$field}) {
                        Storage::disk('public')->delete($aboutUs->{$field});
                    }
                    $aboutUs->{$field} = $request->file($field)->store('aboutUs', 'public');
                }
            }

            $aboutUs->save();

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
            $aboutUs = AboutUs::findOrFail($request->id);

            $imageFields = ['img_1', 'img_2'];
            foreach ($imageFields as $field) {
                if ($aboutUs->{$field}) {
                    Storage::disk('public')->delete($aboutUs->{$field});
                }
            }

            $aboutUs->delete();

            return redirect()->back()->with('success', 'Record deleted successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Record not found.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
