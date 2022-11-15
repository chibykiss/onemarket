<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserCategoryController;
use App\Http\Controllers\UserController;
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
    //unathenticated routes
    Route::group(["middleware" => ["auth:sanctum"]], function(){
        Route::group(["middleware" => ["auth.admin"]], function(){
            Route::apiResource('/user_cat', UserCategoryController::class);
            Route::post('/register', [AuthController::class, 'register']);
            Route::put('/update/{user}', [AuthController::class, 'testupdate']);
            // Route::apiResource('/user', UserController::class);
            // Route::apiResource('/shop', ShopController::class);
            // Route::apiResource('/owner', OwnerController::class);
            Route::apiResources([
                '/user' => UserController::class,
                '/shop' => ShopController::class,
                '/owner' => OwnerController::class,
            ]);
            //Route::post('/shop/add', [ShopController::class, 'shopowner']);
            Route::put('/updateowner/{owner}', [OwnerController::class,'updateOwner']);
            Route::group(["middleware" => ["isSuperAdmin"]], function(){
                Route::apiResource('/admin', AdminController::class);
                Route::controller(ApprovalController::class)->group(function () {
                    Route::get('/approve/user/{user}', 'approveUser');
                    Route::get('/approve/shop/{shop}', 'approveShop');
                    Route::get('/approve/owner/{owner}', 'approveOwner');
                });
                //Route::get('/approve/user/{user}', [ApprovalController::class, 'approvemember']);
                Route::get('/person',[UserController::class, 'getUsercategory']);
            });
        });
       Route::get('/logout', [AuthController::class, 'logout']);
    });
    // Route::get('/user_cat',[UserCategoryController::class,'index']);
    // Route::get('/user_cat/{cat}',[UserCategoryController::class,'show']);
    Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotpassword']);
    Route::post('/verify-code', [ForgotPasswordController::class, 'verifycode']);
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetpassword']);
    Route::post('/login', [AuthController::class, 'login']);
});

