<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Models\Book;
use App\Models\Author;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/books/', [BookController::class,'index']);
Route::get('/books/find/{term}', [BookController::class,'find']);
Route::get('/books/{id}', [BookController::class,'show']);

Route::get('/authors', [AuthorController::class,'index']);
Route::get('/authors/{id}', [AuthorController::class,'show']);
Route::get('/authors/find/{term}', [AuthorController::class,'find']);
