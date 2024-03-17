<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\TenantController;

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

Route::post('v1/auth/login', [AuthController::class, 'login']); 
Route::post('v1/auth/signup', [AuthController::class, 'signup']);
Route::get('v1/tenant/onboard', [TenantController::class, 'onboardTenant']);


Route::group(['middleware' => ['checkAuth'], 'prefix' => 'v1/auth', 'namespace' => 'Api'], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});

Route::group(['middleware' => [], 'prefix' => 'v1/tenant', 'namespace' => 'Api'], function () {
    Route::post('complaints', [TenantController::class, 'makeComplaints']);
    Route::get('complaints', [TenantController::class, 'getComplaints']);
    Route::get('complaint', [TenantController::class, 'getComplaint']);
    Route::put('complaint', [TenantController::class, 'updateComplaint']);
    Route::delete('complaint', [TenantController::class, 'deleteComplaint']);
    Route::post('get-tenant-complaints', [TenantController::class, 'getTenantComplaints']);
});

Route::group(['middleware' => [], 'prefix' => 'v1/owner', 'namespace' => 'Api'], function () {
    Route::post('properties', [OwnerController::class, 'createProperty']);
    Route::get('all-properties', [OwnerController::class, 'getProperties']);
    Route::get('property', [OwnerController::class, 'getProperty']);
    Route::put('property', [OwnerController::class, 'updateProperty']);
    Route::delete('property', [OwnerController::class, 'deleteProperty']);
    Route::post('send-onboarding-link', [OwnerController::class, 'sendOnboardingLinkToTenant']);
});

Route::group(['middleware' => ['checkAuth'], 'prefix' => 'v1/admin', 'namespace' => 'Api'], function () {
    Route::post('approve-property', [AdminController::class, 'approveProperty']);
    Route::post('decline-property', [AdminController::class, 'declineProperty']);
});