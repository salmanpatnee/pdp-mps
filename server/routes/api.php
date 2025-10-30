<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\ArticleTypesController;
use App\Http\Controllers\JournalsController;
use App\Http\Controllers\ManuscriptController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\TempUploadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(["auth:sanctum"])->get("/user", function (Request $request) {
    return $request->user();
});
Route::post("/login", [AuthController::class, "login"]);
Route::middleware(["auth:sanctum"])->post("/logout", [
    AuthController::class,
    "logout",
]);

Route::middleware(["auth:sanctum"])->group(function () {
    Route::apiResource("journals", JournalsController::class);
    Route::apiResource("roles", RolesController::class)->only([
        "index",
        "show",
    ]);
    Route::apiResource("users", UsersController::class);
    Route::get("/authors/search", [AuthorController::class, "search"]);
    Route::apiResource("article_types", ArticleTypesController::class)->only([
        "index",
        "show",
    ]);

    Route::apiResource("manuscripts", ManuscriptController::class);
    Route::post("/manuscripts/upload-temp", [
        ManuscriptController::class,
        "uploadTemp",
    ]);
    Route::get("/manuscript-files/{manuscriptFile}/download", [
        ManuscriptController::class,
        "downloadFile",
    ]);

    Route::post("/temp-upload", [TempUploadController::class, "upload"]); // New temporary upload route
});
