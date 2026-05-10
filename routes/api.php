<?php
use App\Http\Controllers\Api\{AnalyticsController,AuthController,InventoryController,MaterialController,MaterialPurchaseController,MaterialWriteOffController,ProjectController,ServiceEntryController};use Illuminate\Support\Facades\Route;
Route::post('/login',[AuthController::class,'login']);
Route::middleware('auth:sanctum')->group(function(){
    Route::post('/logout',[AuthController::class,'logout']); Route::get('/user',[AuthController::class,'user']);
    Route::apiResource('projects',ProjectController::class);
    Route::get('/projects/{project}/materials',[MaterialController::class,'index']); Route::post('/projects/{project}/materials',[MaterialController::class,'store']); Route::apiResource('materials',MaterialController::class)->except(['index','store']);
    Route::get('/projects/{project}/material-purchases',[MaterialPurchaseController::class,'index']); Route::post('/projects/{project}/material-purchases',[MaterialPurchaseController::class,'store']); Route::apiResource('material-purchases',MaterialPurchaseController::class)->parameters(['material-purchases'=>'purchase'])->except(['index','store']);
    Route::get('/projects/{project}/material-write-offs',[MaterialWriteOffController::class,'index']); Route::post('/projects/{project}/material-write-offs',[MaterialWriteOffController::class,'store']); Route::apiResource('material-write-offs',MaterialWriteOffController::class)->parameters(['material-write-offs'=>'writeOff'])->except(['index','store']);
    Route::get('/projects/{project}/service-entries',[ServiceEntryController::class,'index']); Route::post('/projects/{project}/service-entries',[ServiceEntryController::class,'store']); Route::apiResource('service-entries',ServiceEntryController::class)->parameters(['service-entries'=>'serviceEntry'])->except(['index','store']);
    Route::get('/projects/{project}/inventory',[InventoryController::class,'index']); Route::get('/projects/{project}/inventory/movements',[InventoryController::class,'movements']); Route::post('/projects/{project}/inventory/adjustments',[InventoryController::class,'adjustment']);
    Route::get('/projects/{project}/analytics/summary',[AnalyticsController::class,'summary']); Route::get('/projects/{project}/analytics/by-tags',[AnalyticsController::class,'byTags']); Route::get('/projects/{project}/analytics/by-months',[AnalyticsController::class,'byMonths']); Route::get('/projects/{project}/analytics/by-contractors',[AnalyticsController::class,'byContractors']); Route::get('/projects/{project}/analytics/by-materials',[AnalyticsController::class,'byMaterials']);
});
