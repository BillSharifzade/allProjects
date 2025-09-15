<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BlogsRequest;
use App\Models\Blogs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogsController extends Controller
{
    public function index(){
        $blogs = Blogs::orderBy('id','desc')->get();
        return view('pages.admin.blogs.index',compact('blogs'));
    }

    public function store(BlogsRequest $request){
        try {
            $blogs = Blogs::create($request->validated());
            if ($request->hasFile('img')) {
                $blogs->img = $request->file('img')->store('Blogs', 'public');
            }

            $blogs->save();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while updating the page.');
        }
        return redirect()->back()->with('success', 'Page updated successfully');
    }

    public function getById($id)
    {
        try {
            $blogs = Blogs::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $blogs
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(BlogsRequest $request)
    {
        try {
            $blogs = Blogs::findOrFail($request->id);

            $blogs->update($request->validated());

            if ($request->hasFile('img')) {
                if ($blogs->img) {
                    Storage::disk('public')->delete($blogs->img);
                }
                $blogs->img = $request->file('img')->store('Blogs', 'public');
            }

            $blogs->save();

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
            $blogs = Blogs::findOrFail($request->id);

            if ($blogs->img) {
                Storage::disk('public')->delete($blogs->img);
            }

            $blogs->delete();

            return redirect()->back()->with('success', 'Record deleted successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Record not found.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function changeStatus($id, $status){
        try {
            $blogs = Blogs::findOrFail($id);
            $blogs->update(['status' => $status]);
            $blogs->save();

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
