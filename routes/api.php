<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ListCarController;
use App\Http\Controllers\Api\userDetial;
use App\Http\Controllers\Mechanic\MechanicController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
// Route::get('all_user',[ApiController::class , 'all_user']);
Route::controller(ApiController::class)->group(function () {
    Route::get('all_user', 'all_user');
    Route::post('forgot_password', 'forgot_password');
    Route::post('otp_verification', 'otp_verification');
    Route::post('reset_password', 'reset_password');
});

// Route::get('listing-user', [AdminController::class, 'user_listing']);
// Route::get('mechanic-listing', [AdminController::class, 'mechanic_listing']);
// Route::get('cars-listing', [AdminController::class, 'cars_listing']);
// Route::get('car-ads-listing', [AdminController::class, 'ads_listing']);
// Route::get('mechanic-status-change/{user}', [AdminController::class, 'mechanic_status_change']);
Route::get('car-services-listing', [AdminController::class, 'car_services_listing']);

Route::controller(AdminController::class)->group(function () {
    Route::post('inquiries-add','inquiry_add');
});

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::middleware(['admin'])->group(function () {
        Route::controller(AdminController::class)->group(function () {
            Route::get('/admin/listing-user', 'user_listing');
            Route::get('/admin/mechanic-listing', 'mechanic_listing');
            Route::get('/admin/view-user/{id}','view_user');
            Route::get('/admin/view-mechanic/{id}','view_mechanic');
            Route::get('/admin/cars-listing', 'cars_listing');
            Route::get('/admin/car-ads-listing', 'ads_listing');
            Route::get('/admin/mechanic-status-change/{user}', 'mechanic_status_change');
            Route::get('/admin/car-services-listing', 'car_services_listing');
            Route::post('/admin/car-service-add-update/{car?}', 'car_service_add_update');
            Route::get('/admin/car-service-delete/{car}', 'car_service_delete');
            Route::get('/admin/car-service-view/{car}', 'view_car_service');
            Route::get('/admin/request-inspection-new-listing', 'request_inspection_new_listing');
            Route::get('/admin/request-inspection-inprogress-listing', 'request_inspection_inprogress_listing');
            Route::get('/admin/request-inspection-completed-listing', 'request_inspection_completed_listing');
            Route::get('/admin/inquiries-listing','inquiry_listing');
            Route::get('/admin/total-earning','request_inspection_earning');
            Route::post('/admin/category-add-update/{category?}', 'category_add_update');
            Route::get('/admin/category-listing', 'category_listing');
            Route::get('/admin/delete-category/{category}', 'catgeory_delete');
            Route::post('/admin/sub-category-add-update/{subcategory?}', 'sub_category_add_update');
            Route::get('/admin/sub-category-listing/{category_id}', 'sub_category_listing');
            Route::get('/admin/delete-sub-category/{category_id}/{subcategory_id}', 'sub_catgeory_delete');
            Route::post('/admin/report-question-add-update/{question_id?}','add_update_inspection_report_question');
            Route::get('/admin/report-question-listing','question_listing');
            Route::get('/admin/report-question-view/{question}','question_view');
            Route::get('/admin/report-question-search/{category_id}/{sub_category_id?}','question_search');
            Route::get('/admin/delete-question/{question}','question_delete');
            Route::get('/admin/report-inspection','report_inspection');
        });
    });

    Route::middleware(['mechanic'])->group(function () {
        Route::controller(MechanicController::class)->group(function () {
            Route::post('/mechanic/mechanic-detail-add-update', 'mechanic_detail_add_update');
            Route::get('/mechanic/view-profile', 'view_profile');
            Route::get('/mechanic/mechanic-listing', 'mechanic_listing');
            Route::post('/mechanic/image-upload', 'mechanic_image_upload');
            Route::get('/mechanic/search_filter', 'mechanic_search');
            Route::get('/mechanic/inspection_list', 'mechanic_inspection_list');
            Route::post('/mechanic/request-inspection-pick/{reqId}', 'request_inspection_pick');
            Route::get('/my-request-inspection-list', 'my_mechanic_inspection_list');
            Route::get('/my-completed-request-inspection-list', 'my_completed_mechanic_inspection_list');
            Route::get('/mechanic/request-inspection-earning','request_inspection_earning');
            Route::get('/mechanic/report-inspection','report_inspection');
            Route::post('/mechanic/report-inspection-complete','report_inpsection_submit');

        });

        Route::controller(ListCarController::class)->group(function () {
            Route::get('view-request-inspection/{reqId}', 'request_inspection_view');
        });
    });

    Route::middleware(['user'])->group(function () {
        Route::controller(MechanicController::class)->group(function () {
            Route::get('/mechanic/mechanic-listing', 'mechanic_listing');
        });

        //For Logout
        Route::controller(ApiController::class)->group(function () {
            Route::get('logout', 'userLogout');
            Route::get('user-detail', 'userDetail');
            Route::post('update-detail', 'userUpdate');
        });

        //For Post Car Add
        Route::controller(ListCarController::class)->group(function () {
            Route::post('post-add', 'store');
            Route::post('post-edit/{listCar}', 'update');
            Route::get('all-cars/{listCar?}', 'index');
            Route::get('my-cars-list', 'myCarsList');
            Route::get('users_car', 'users_car');
            Route::post('car-ad-add-update/{carAd?}', 'cars_ad_add_update');
            Route::get('car-ad-listing', 'car_ad_listing');
            Route::get('my-car-ad-listing', 'my_car_ad_listing');
            Route::get('car-ad-safe-unsafe/{carAd}', 'car_ad_save_unsafe');
            Route::get('car-ad-safe-listing', 'car_ad_save_listing');
            Route::get('view-car-ad/{car_ad_id}', 'view_car_ad');
            Route::get('car-ad-delete/{car_ad_id}', 'car_ad_delete');
            Route::get('car-image-delete/{car_id}/{id}', 'car_images_delete');
            Route::post('request-inspection', 'request_inspection');
            Route::get('report-inspection/{listCar}','report_inspection');
            // Route::get('view-request-inspection/{reqId}', 'request_inspection_view');
            // Route::get('specific-car/{listCar}','specific_car');
        });

        //For User Profiles
        Route::controller(userDetial::class)->group(function () {
            Route::post('update-image', 'update');
        });
    });


    //chat user
    Route::controller(ChatController::class)->group(function () {
        Route::get('get_group_id', 'get_group_id');
    });
});

//For Authentication
Route::controller(ApiController::class)->group(function () {
    Route::post('login-with-google','loginwithgoogle');
    Route::post('mechanic-register', 'registerMechanic');
    Route::post('mechanic-login', 'loginMechanic');
    Route::post('register', 'registerUser');
    Route::post('login', 'loginUser');
});

// Route::any('/login',function () {
//     return Response()->json(["status"=>false,'msg'=>'Token is Wrong OR Did not Exist!']);
// }
// )->name('login');
