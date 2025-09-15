<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServicesRequest;
use App\Models\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServicesController extends Controller
{
    public function index(){
        $services = Services::orderBy('id','desc')->get();
        return view('pages.admin.services.index', compact('services'));
    }

    public function store(ServicesRequest $request){
        try {
            $services = Services::create($request->validated());
            if ($request->hasFile('img')) {
                $services->img = $request->file('img')->store('Services', 'public');
            }

            $services->save();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while updating the page.');
        }
        return redirect()->back()->with('success', 'Page updated successfully');
    }

    public function getById($id)
    {
        try {
            $services = Services::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $services
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(ServicesRequest $request)
    {
        try {
            $services = Services::findOrFail($request->id);

            $services->update($request->validated());

            if ($request->hasFile('img')) {
                if ($services->img) {
                    Storage::disk('public')->delete($services->img);
                }
                $services->img = $request->file('img')->store('Services', 'public');
            }

            $services->save();

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
            $services = Services::findOrFail($request->id);

            if ($services->img) {
                Storage::disk('public')->delete($services->img);
            }

            $services->delete();

            return redirect()->back()->with('success', 'Record deleted successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Record not found.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function changeStatus($id, $status){
        try {
            $services = Services::findOrFail($id);
            $services->update(['status' => $status]);
            $services->save();

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
