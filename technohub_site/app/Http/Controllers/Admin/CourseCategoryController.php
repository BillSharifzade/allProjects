<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseCategoryRequest;
use App\Models\CourseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseCategoryController extends Controller
{
    public function index(){
        $courseCategories = CourseCategory::orderBy('id', 'desc')->get();
        return view('pages.admin.courseCategory.index', compact('courseCategories'));
    }

    public function store(CourseCategoryRequest $request){
        try {
            $courseCategories = CourseCategory::create($request->validated());

            if ($request->hasFile('img')) {
                $courseCategories->img = $request->file('img')->store('CourseCategory', 'public');
            }

            $courseCategories->save();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while updating the page.');
        }
        return redirect()->route('courseCategory')->with('success', 'Page updated successfully');
    }

    public function getById($id)
    {
        try {
            $courseCategory = CourseCategory::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $courseCategory
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(CourseCategoryRequest $request)
    {
        try {
            $courseCategories = CourseCategory::findOrFail($request->id);

            $courseCategories->update($request->validated());

            if ($request->hasFile('img')) {
                if ($courseCategories->img) {
                    Storage::disk('public')->delete($courseCategories->img);
                }
                $courseCategories->img = $request->file('img')->store('CourseCategory', 'public');
            }

            $courseCategories->save();

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
            $courseCategories = CourseCategory::findOrFail($request->id);

            if ($courseCategories->img) {
                Storage::disk('public')->delete($courseCategories->img);
            }

            $courseCategories->delete();

            return redirect()->back()->with('success', 'Record deleted successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Record not found.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function changeStatus($id, $status){
        try {
            $courseCategories = CourseCategory::findOrFail($id);
            $courseCategories->update(['status' => $status]);
            $courseCategories->save();

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
