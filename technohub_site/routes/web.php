<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BlogsController;
use App\Http\Controllers\Admin\TeamsController;
use App\Http\Controllers\Admin\EventsController;
use App\Http\Controllers\Admin\AboutUsController;
use App\Http\Controllers\Admin\CoursesController;
use App\Http\Controllers\Admin\PartnersController;
use App\Http\Controllers\Admin\ServicesController;
use App\Http\Controllers\Admin\AboutQwasarController;
use App\Http\Controllers\Admin\QwasarAboutController;
use App\Http\Controllers\Admin\QwasarPathsController;
use App\Http\Controllers\Admin\CourseCategoryController;
use App\Http\Controllers\Admin\QwasarServicesController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::controller(AdminController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard');
    });

    Route::prefix('dashboard')->group(function () {
        Route::controller(AboutUsController::class)->prefix('/aboutUs')->group(callback: function () {
            Route::get('/', 'index')->name('aboutUs');
            Route::post('/store', 'store')->name('aboutUs.store');
            Route::get('/getById/{id}', 'getById')->name('aboutUs.getById');
            Route::put('/update', 'update')->name('aboutUs.update');
            Route::delete('/delete', 'delete')->name('aboutUs.delete');
        });

        Route::controller(CourseCategoryController::class)->prefix('/courseCategory')->group(callback: function () {
            Route::get('/', 'index')->name('courseCategory');
            Route::post('/store', 'store')->name('courseCategory.store');
            Route::get('/getById/{id}', 'getById')->name('courseCategory.getById');
            Route::put('/update', 'update')->name('courseCategory.update');
            Route::delete('/delete', 'delete')->name('courseCategory.delete');
            Route::get('/changeStatus/{id}/{status}', 'changeStatus')->name('courseCategory.changeStatus');
        });

        Route::controller(CoursesController::class)->prefix('/courses')->group(callback: function () {
            Route::get('/', 'index')->name('courses');
            Route::post('/store', 'store')->name('courses.store');
            Route::get('/getById/{id}', 'getById')->name('courses.getById');
            Route::put('/update', 'update')->name('courses.update');
            Route::delete('/delete', 'delete')->name('courses.delete');
            Route::get('/changeStatus/{id}/{status}', 'changeStatus')->name('courses.changeStatus');
        });

        Route::controller(TeamsController::class)->prefix('/teams')->group(callback: function () {
            Route::get('/', 'index')->name('teams');
            Route::post('/store', 'store')->name('teams.store');
            Route::get('/getById/{id}', 'getById')->name('teams.getById');
            Route::put('/update', 'update')->name('teams.update');
            Route::delete('/delete', 'delete')->name('teams.delete');
            Route::get('/changeStatus/{id}/{status}', 'changeStatus')->name('teams.changeStatus');
        });

        Route::controller(EventsController::class)->prefix('/events')->group(callback: function () {
            Route::get('/', 'index')->name('events');
            Route::post('/store', 'store')->name('events.store');
            Route::get('/getById/{id}', 'getById')->name('events.getById');
            Route::put('/update', 'update')->name('events.update');
            Route::delete('/delete', 'delete')->name('events.delete');
            Route::get('/changeStatus/{id}/{status}', 'changeStatus')->name('events.changeStatus');
        });

        Route::controller(ServicesController::class)->prefix('/services')->group(callback: function () {
            Route::get('/', 'index')->name('services');
            Route::post('/store', 'store')->name('services.store');
            Route::get('/getById/{id}', 'getById')->name('services.getById');
            Route::put('/update', 'update')->name('services.update');
            Route::delete('/delete', 'delete')->name('services.delete');
            Route::get('/changeStatus/{id}/{status}', 'changeStatus')->name('services.changeStatus');
        });

        Route::controller(BlogsController::class)->prefix('/blogs')->group(callback: function () {
            Route::get('/', 'index')->name('blogs');
            Route::post('/store', 'store')->name('blogs.store');
            Route::get('/getById/{id}', 'getById')->name('blogs.getById');
            Route::put('/update', 'update')->name('blogs.update');
            Route::delete('/delete', 'delete')->name('blogs.delete');
            Route::get('/changeStatus/{id}/{status}', 'changeStatus')->name('blogs.changeStatus');
        });

        Route::controller(AboutQwasarController::class)->prefix('/aboutQwasar')->group(callback: function () {
            Route::get('/', 'index')->name('aboutQwasar');
            Route::post('/store', 'store')->name('aboutQwasar.store');
            Route::get('/getById/{id}', 'getById')->name('aboutQwasar.getById');
            Route::put('/update', 'update')->name('aboutQwasar.update');
            Route::delete('/delete', 'delete')->name('aboutQwasar.delete');
            Route::get('/changeStatus/{id}/{status}', 'changeStatus')->name('aboutQwasar.changeStatus');
        });

        Route::controller(PartnersController::class)->prefix('/partners')->group(callback: function () {
            Route::get('/', 'index')->name('partners');
            Route::post('/store', 'store')->name('partners.store');
            Route::get('/getById/{id}', 'getById')->name('partners.getById');
            Route::put('/update', 'update')->name('partners.update');
            Route::delete('/delete', 'delete')->name('partners.delete');
            Route::get('/changeStatus/{id}/{status}', 'changeStatus')->name('partners.changeStatus');
        });

        Route::controller(QwasarServicesController::class)->prefix('/qwasarServices')->group(callback: function () {
            Route::get('/', 'index')->name('qwasarServices');
            Route::post('/store', 'store')->name('qwasarServices.store');
            Route::get('/getById/{id}', 'getById')->name('qwasarServices.getById');
            Route::put('/update', 'update')->name('qwasarServices.update');
            Route::delete('/delete', 'delete')->name('qwasarServices.delete');
            Route::get('/changeStatus/{id}/{status}', 'changeStatus')->name('qwasarServices.changeStatus');
        });

        Route::controller(QwasarPathsController::class)->prefix('/qwasarPaths')->group(callback: function () {
            Route::get('/', 'index')->name('qwasarPaths');
            Route::post('/store', 'store')->name('qwasarPaths.store');
            Route::get('/getById/{id}', 'getById')->name('qwasarPaths.getById');
            Route::put('/update', 'update')->name('qwasarPaths.update');
            Route::delete('/delete', 'delete')->name('qwasarPaths.delete');
            Route::get('/changeStatus/{id}/{status}', 'changeStatus')->name('qwasarPaths.changeStatus');
        });
    });
});

Route::get('/', [SiteController::class, 'index'])->name('home');
Route::get('/abouts', [SiteController::class, 'abouts'])->name('abouts');
Route::get('/blog', [SiteController::class, 'blog'])->name('blog');
Route::get('/blog-details/{id}', [SiteController::class, 'blogDetails'])->name('blog.details');
Route::get('/contacts', [SiteController::class, 'contacts'])->name('contacts');
Route::get('/courses/details/{id}', [SiteController::class, 'coursesDetails'])->name('courses.details');
Route::post('/request', [SiteController::class, 'request'])->name('request');
Route::get('/success', [SiteController::class, 'success'])->name('success');
Route::get('/qwasar', [SiteController::class, 'qwasar'])->name('qwasar');

require __DIR__.'/auth.php';
