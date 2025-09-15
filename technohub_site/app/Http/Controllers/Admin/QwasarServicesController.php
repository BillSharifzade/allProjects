<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\QwasarServices;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\QwasarServicesRequest;

class QwasarServicesController extends Controller
{
    public function index(){
        $services = QwasarServices::orderBy('id','desc')->get();
        return view('pages.admin.qwasarServices.index', compact('services'));
    }

    public function store(QwasarServicesRequest $request){
        try {
            $services = QwasarServices::create($request->validated());
            if ($request->hasFile('img')) {
                $services->img = $request->file('img')->store('QwasarServices', 'public');
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
            $services = QwasarServices::findOrFail($id);

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

    public function update(QwasarServicesRequest $request)
    {
        try {
            $services = QwasarServices::findOrFail($request->id);

            $services->update($request->validated());

            if ($request->hasFile('img')) {
                if ($services->img) {
                    Storage::disk('public')->delete($services->img);
                }
                $services->img = $request->file('img')->store('QwasarServices', 'public');
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
            $services = QwasarServices::findOrFail($request->id);

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
            $services = QwasarServices::findOrFail($id);
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
