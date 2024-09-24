<?php
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\IndexController;
use App\Http\Controllers\Api\UtilController;


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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


  Route::get('/', function () {
      return response()->json(['message' => 'Jobs API', 'status' => 'Connected']);
  });
  Route::apiResource('index', IndexController::class);
    Route::apiResource('util', UtilController::class);
    Route::get('sendEmail','Api\UtilController@sendEmail');



