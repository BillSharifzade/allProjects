<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CoursesRequest;
use App\Models\CourseCategory;
use App\Models\Courses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CoursesController extends Controller
{
    public function index(){
        $courses = Courses::orderBy('id','desc')->get();
        $courseCategories = CourseCategory::orderBy('id','desc')->get();
        return view('pages.admin.courses.index', compact('courses', 'courseCategories'));
    }

    public function store(CoursesRequest $request){
        try {
            $courses = Courses::create($request->validated());

            if ($request->hasFile('img')) {
                $courses->img = $request->file('img')->store('Courses', 'public');
            }

            $courses->save();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while updating the page.');
        }
        return redirect()->back()->with('success', 'Page updated successfully');
    }

    public function getById($id)
    {
        try {
            $courses = Courses::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $courses
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(CoursesRequest $request)
    {
        try {
            $course = Courses::findOrFail($request->id);

            $course->update($request->validated());

            if ($request->hasFile('img')) {
                if ($course->img) {
                    Storage::disk('public')->delete($course->img);
                }
                $course->img = $request->file('img')->store('Courses', 'public');
            }

            $course->save();

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
            $course = Courses::findOrFail($request->id);

            if ($course->img) {
                Storage::disk('public')->delete($course->img);
            }

            $course->delete();

            return redirect()->back()->with('success', 'Record deleted successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Record not found.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function changeStatus($id, $status){
        try {
            $courses = Courses::findOrFail($id);
            $courses->update(['status' => $status]);
            $courses->save();

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

