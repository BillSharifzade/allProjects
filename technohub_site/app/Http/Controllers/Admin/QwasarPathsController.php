<?php

namespace App\Http\Controllers\Admin;

use App\Models\QwasarPaths;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\QwasarPathsRequest;

class QwasarPathsController extends Controller
{
    public function index(){
        $paths = QwasarPaths::orderBy('id','desc')->get();
        return view('pages.admin.qwasarPaths.index',compact('paths'));
    }

    public function store(QwasarPathsRequest $request){
        try {
            $paths = QwasarPaths::create($request->validated());
            if ($request->hasFile('img')) {
                $paths->img = $request->file('img')->store('Paths', 'public');
            }

            $paths->save();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while updating the page.');
        }
        return redirect()->back()->with('success', 'Page updated successfully');
    }

    public function getById($id)
    {
        try {
            $paths = QwasarPaths::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $paths
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(QwasarPathsRequest $request)
    {
        try {
            $paths = QwasarPaths::findOrFail($request->id);

            $paths->update($request->validated());

            if ($request->hasFile('img')) {
                if ($paths->img) {
                    Storage::disk('public')->delete($paths->img);
                }
                $paths->img = $request->file('img')->store('Paths', 'public');
            }

            $paths->save();

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
            $paths = QwasarPaths::findOrFail($request->id);

            if ($paths->img) {
                Storage::disk('public')->delete($paths->img);
            }

            $paths->delete();

            return redirect()->back()->with('success', 'Record deleted successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Record not found.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function changeStatus($id, $status){
        try {
            $paths = QwasarPaths::findOrFail($id);
            $paths->update(['status' => $status]);
            $paths->save();

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
