<?php

namespace App\Http\Controllers;

use App\Models\Blogs;
use App\Models\AboutUs;
use App\Models\Courses;
use App\Models\Partners;
use App\Models\Requests;
use App\Models\Services;
use App\Models\AboutQwasar;
use App\Models\QwasarServices;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index(){
        $abouts = AboutUs::first();
        $courses = Courses::orderBy('id','desc')->where('status', 1)->get();
        $aboutQwasar = AboutQwasar::first();
        return view('pages.site.index', compact('abouts', 'courses', 'aboutQwasar'));
    }

    public function abouts(){
        $abouts = AboutUs::first();
        $partners = Partners::orderBy('id', 'desc')->where('status', 1)->get();
        $services = Services::orderBy('id', 'desc')->where('status', 1)->get();
        return view('pages.site.abouts', compact('abouts', 'partners', 'services'));
    }

    public function request(Request $request){
        if(Requests::create($request->all())){
            return "Ваше сообщение принято в обработку!";
        }else{
            return "Ошибка! Повторите попытку";
        }
        
    }

    public function blog(){
        $blogs = Blogs::where('status', 1)->orderBy('id', 'desc')->get();
        return view('pages.site.blog', compact('blogs'));
    }

    public function blogDetails($id){
        $blogs = Blogs::find($id);
        return view('pages.site.blog-details', compact('blogs'));
    }

    public function contacts(){
        return view('pages.site.contacts');
    }

    public function coursesDetails($id){
        $course = Courses::find($id);
        return view('pages.site.course-details', compact('course'));
    }

    public function qwasar(){
        $services = QwasarServices::where('status', 1)->orderBy('id', 'desc')->get();
        return view('pages.site.qwasar.index', compact('services'));
    }
}
