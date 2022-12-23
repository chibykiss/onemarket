<?php

use App\Http\Controllers\v1\AdminController;
use App\Http\Controllers\v1\ApprenticeController;
use App\Http\Controllers\v1\ApprovalController;
use App\Http\Controllers\v1\AttacheeController;
use App\Http\Controllers\v1\AuthController;
use App\Http\Controllers\v1\DisapprovalController;
use App\Http\Controllers\v1\ForgotPasswordController;
use App\Http\Controllers\v1\OwnerController;
use App\Http\Controllers\v1\ShopController;
use App\Http\Controllers\v1\UserCategoryController;
use App\Http\Controllers\v1\UserController;
use App\Models\UserCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::prefix('v1')->group(function () {

    Route::group(["middleware" => ["auth:sanctum"]], function(){

        //this middleware is to show that the user is an admin
        Route::group(["middleware" => ["auth.admin"]], function(){
            Route::apiResource('/user_cat', UserCategoryController::class);
            Route::post('/register', [AuthController::class, 'register']);
            Route::put('/update/{user}', [AuthController::class, 'testupdate']);
            // Route::apiResource('/user', UserController::class);
            // Route::apiResource('/shop', ShopController::class);
            // Route::apiResource('/owner', OwnerController::class);
            Route::apiResources([
                '/member' => UserController::class,
                '/shop' => ShopController::class,
                '/owner' => OwnerController::class,
                '/attachee' => AttacheeController::class,
                '/apprentice' => ApprenticeController::class,
            ]);
            //Route::post('/shop/add', [ShopController::class, 'shopowner']);
            Route::put('/updateowner/{owner}', [OwnerController::class,'updateOwner']);
            
            //this routes uses the superadmin middleware
            Route::group(["middleware" => ["isSuperAdmin"]], function(){
                Route::apiResource('/admin', AdminController::class);

                //Aprove controller
                Route::controller(ApprovalController::class)->group(function () {
                    Route::get('/approve/member/{member}', 'approveUser');
                    Route::get('/approve/shop/{shop}', 'approveShop');
                    Route::get('/approve/owner/{owner}', 'approveOwner');
                });

                //Disaprove Controller
                Route::controller(DisapprovalController::class)->group(function(){
                    Route::get('/disapprove/member/{member}','disapproveUser');
                    Route::get('/disapprove/shop/{shop}','disapproveShop');
                    Route::get('/disapprove/owner/{owner}','disapproveOwner');
                });


                //Route::get('/approve/user/{user}', [ApprovalController::class, 'approvemember']);
                Route::get('/person',[UserController::class, 'getUsercategory']);
            });

        });


       Route::get('/logout', [AuthController::class, 'logout']);
    });

    //this is for route that dosent need the user to be logged in
    // Route::get('/user_cat',[UserCategoryController::class,'index']);
    // Route::get('/user_cat/{cat}',[UserCategoryController::class,'show']);
    Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotpassword']);
    Route::post('/verify-code', [ForgotPasswordController::class, 'verifycode']);
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetpassword']);
    Route::post('/login', [AuthController::class, 'login']);
});

