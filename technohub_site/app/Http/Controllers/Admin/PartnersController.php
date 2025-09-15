<?php

namespace App\Http\Controllers\Admin;

use App\Models\Partners;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PartnersRequest;
use Illuminate\Support\Facades\Storage;

class PartnersController extends Controller
{
    public function index(){
        $partners = Partners::orderBy('id','desc')->get();
        return view('pages.admin.partners.index', compact('partners'));
    }

    public function store(PartnersRequest $request){
        try {
            $partners = Partners::create($request->validated());

            if ($request->hasFile('img')) {
                $partners->img = $request->file('img')->store('Partners', 'public');
            }

            $partners->save();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while updating the page.');
        }
        return redirect()->back()->with('success', 'Page updated successfully');
    }

    public function getById($id)
    {
        try {
            $partners = Partners::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $partners
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(PartnersRequest $request)
    {
        try {
            $partners = Partners::findOrFail($request->id);

            $partners->update($request->validated());

            if ($request->hasFile('img')) {
                if ($partners->img) {
                    Storage::disk('public')->delete($partners->img);
                }
                $partners->img = $request->file('img')->store('partners', 'public');
            }

            $partners->save();

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
            $partners = Partners::findOrFail($request->id);

            if ($partners->img) {
                Storage::disk('public')->delete($partners->img);
            }

            $partners->delete();

            return redirect()->back()->with('success', 'Record deleted successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Record not found.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function changeStatus($id, $status){
        try {
            $partners = Partners::findOrFail($id);
            $partners->update(['status' => $status]);
            $partners->save();

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
