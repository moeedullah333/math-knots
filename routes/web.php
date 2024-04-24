<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\http\Controllers\Admin\carListController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});
Route::group(['middleware' => ['auth', 'admin'],'prefix'=>'admin'], function () {
    // Route::middleware(['auth','admin' , 'verified'])->group(function () {
    Route::get('/dashboard',[AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    //For Cars Route
    Route::controller(carListController::class)->group(function(){
        Route::get('cars','index');
        Route::get('cars/{id}','show')->name('car.detail');
    });

    //For Update and View Profile
    

});

// Route::resource('admin/cars', carListController::class);




require __DIR__.'/auth.php';
