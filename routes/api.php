<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Artisan;

Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
Route::post('/password/reset', [UserController::class, 'resetPassword']);
Route::post('/password/reset-request', [NotificationController::class, 'sendResetLinkEmail']);



Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});


// User Controls
Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('role:superadmin')->group(function () {
        Route::get('/getAllUsers', [UserController::class, 'getAllUsers']);
        Route::put('/updateUserRole/{id}', [UserController::class, 'updateRole']);
        Route::put('/updateUserDepartment/{id}', [UserController::class, 'updateDepartment']);
        Route::delete('/deleteUser/{id}', [UserController::class, 'deleteUser']);
    });
    Route::middleware('role:admin')->group(function () {
        Route::get('/getDepartUsers', [UserController::class, 'getDepartUsers']);
    });
    Route::middleware('role:superadmin,admin')->group(function () {
        Route::post('/createUser', [UserController::class, 'createNewUser']);
    });
    Route::get('/fetchTotals', [UserController::class, 'fetchTotals']);
    Route::get('/getUser', [UserController::class, 'getUser']);
    Route::post('/requestSuperAdmin/{id}', [UserController::class, 'requestSuperAdmin']);
    Route::put('/user/{id}', [UserController::class, 'updateUser']);
});



// Post Controls
Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('role:superadmin')->group(function () {
        Route::put('/posts/{id}', [PostController::class, 'updatePostStatus']);
    });
    Route::middleware('role:admin')->group(function () {
        Route::get('/getUnverifiedPosts', [PostController::class, 'getUnverifiedPosts']);
    });
    Route::middleware('role:superadmin,admin')->group(function () {
        Route::delete('/posts/{id}', [PostController::class, 'deletePost']);
    });
    Route::get('/getPosts', [PostController::class, 'index']);
    Route::post('/updatePost/{id}', [PostController::class, 'update']);
    Route::get('/getPersonalPosts', [PostController::class, 'getPersonalPosts']);
    Route::post('/posts', [PostController::class, 'store']);
});



// Role Controls
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/getAllRoles', [RoleController::class, 'getAllRoles']);
});



// Notification Controls
Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('role:superadmin')->group(function () {
        Route::post('/posts/{id}/notifyStatusChange', [NotificationController::class, 'notifyStatusChange']);
    });
    Route::middleware('role:admin')->group(function () {
        Route::post('/notifySuperAdmin/{id}', [NotificationController::class, 'notifySuperAdmin']);
    });
    Route::get('/notifications/fetch/{drop}', [NotificationController::class, 'index']);
    Route::post('/deletePostRequest/{id}', [NotificationController::class, 'deletePostRequest']);
});


// Department Controls
Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('role:superadmin,admin')->group(function () {
        Route::get('/departments', [DepartmentController::class, 'index']);
        Route::get('/optionDepartments', [DepartmentController::class, 'optionDepartment']);
    });
    Route::middleware('role:superadmin')->group(function () {
        Route::post('/departments', [DepartmentController::class, 'store']);
        Route::put('/departments/{id}', [DepartmentController::class, 'update']);
        Route::delete('/departments/{id}', [DepartmentController::class, 'destroy']);
    });
});

