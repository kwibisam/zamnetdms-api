<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DepartmentsController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\DocumentTypesController;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\TagsController;
use App\Http\Controllers\Api\WorkSpaceController;
use Illuminate\Support\Facades\Route;


Route::controller(AuthController::class)->group(function() {

    Route::delete('users/{id}', 'delete')->middleware('auth:sanctum');

    Route::get('/email/verify/{id}/{hash}', 'verifyEmail')->middleware(['auth:sanctum', 'signed'])->name('verification.verify');
    Route::post('register', 'register')->middleware('auth:sanctum');
    Route::post('login', 'login');
    Route::get('users', 'index')->middleware('auth:sanctum');
    Route::get('user','showProfile')->middleware('auth:sanctum');
    Route::get('users/{id}','show')->middleware('auth:sanctum');
    Route::post('logout', 'logout')->middleware('auth:sanctum');

    Route::get('users/{id}', 'show');

    Route::post('change-password/{id}', 'changePassword')->middleware('auth:sanctum');

    Route::put('users/{user_id}/workspaces/{workspace_id}', 'addUserToWorkSpace')->middleware('auth:sanctum');

    Route::delete('users/{user_id}/workspaces/{workspace_id}', 'removeUserFromWorkSpace')->middleware('auth:sanctum');

    Route::delete('users/{user_id}/roles/{role_id}', 'removeUserRole')->middleware('auth:sanctum');
    
    Route::put('users/{user_id}/roles/{role_id}', 'addRoleToUser')->middleware('auth:sanctum');

    Route::put('users/{user_id}/departments/{department_id}', 'updateUserDepartment')->middleware('auth:sanctum');

    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])
                ->middleware('guest')
                ->name('password.forgot');

    Route::post('/reset-password', [AuthController::class, 'resetPassword'])
                ->middleware('guest')
                ->name('password.reset');
});

Route::controller(RolesController::class)
->group(function() {

    Route::post('roles', 'store');
    Route::get('roles','index');
    Route::get('roles/{id}','show');
    Route::put('roles/{id}','update');
    Route::delete('roles/{id}', 'delete');
});

Route::controller(WorkSpaceController::class)
->group(function(){
    Route::post('workspaces', 'store');
    Route::get('workspaces', 'index');
    Route::get('workspaces/{id}', 'show');
    Route::put('workspaces/{id}', 'update');
    Route::delete('workspaces/{id}', 'delete');
});


Route::controller(DepartmentsController::class)
->group(function(){
    Route::post('departments', 'store');
    Route::get('departments', 'index');
    Route::get('departments/{id}', 'show');
    Route::put('departments/{id}', 'update');
    Route::delete('departments/{id}', 'delete');
});



Route::controller(DocumentController::class)
->group(function ()
{
    Route::post('documents', 'store')->middleware('auth:sanctum');
    Route::post('documents/{id}', 'update')->middleware('auth:sanctum');
    Route::get('documents', 'index')->middleware('auth:sanctum');;
    Route::get('documents/recent', 'recentDocuments')->middleware('auth:sanctum');;
    Route::get('documents/{id}', 'show');
    Route::put('documents/{id}', 'update')->middleware('auth:sanctum');
    Route::delete('documents/{id}', 'delete');
    // Route::post('documents', 'testUpdate');
   
});

Route::controller(DocumentTypesController::class)
->group(function ()
{
    Route::post('types', 'store')->middleware('auth:sanctum');
    Route::get('types', 'index');
    Route::get('types/{id}', 'show');
    Route::put('types/{id}', 'update');
    Route::delete('types/{id}', 'delete');
});


Route::controller(TagsController::class)
->group(function ()
{
    Route::post('tags', 'store')->middleware('auth:sanctum');
    Route::get('tags', 'index');
    Route::get('tags/{id}', 'show');
    Route::put('tags/{id}', 'update');
    Route::delete('tags/{id}', 'delete');
});


