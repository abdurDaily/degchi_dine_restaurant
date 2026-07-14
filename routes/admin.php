<?php

use App\Http\Controllers\Backend\BranchController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\MenuController;
use App\Http\Controllers\Backend\SignaturePlatterController;
use App\Http\Controllers\Backend\FacebookReelController;
use App\Http\Controllers\Backend\AboutController;
use App\Http\Controllers\Backend\ContactController;
use App\Http\Controllers\Backend\CouponController;
use Illuminate\Support\Facades\Route;

// --- BRANCH MANAGEMENT ---
Route::middleware(['auth', 'setLocale', 'user.active'])->prefix("branch")->name("branch.")->group(function () {
    Route::get("/index", [BranchController::class, "index"])->name("index");
    Route::post("/store", [BranchController::class, "store"])->name("store");
    Route::get("/{branch}/edit", [BranchController::class, "edit"])->name("edit");
    Route::post("/{branch}/update", [BranchController::class, "update"])->name("update");
    Route::delete("/{branch}/delete", [BranchController::class, "destroy"])->name("delete");
});

// --- CATEGORY MANAGEMENT ---
Route::middleware(['auth', 'setLocale', 'user.active'])->prefix("category")->name("category.")->group(function () {
    Route::get("/index", [CategoryController::class, "index"])->name("index");
    Route::post("/store", [CategoryController::class, "store"])->name("store");
    Route::get("/{category}/edit", [CategoryController::class, "edit"])->name("edit");
    Route::post("/{category}/update", [CategoryController::class, "update"])->name("update");
    Route::delete("/{category}/delete", [CategoryController::class, "destroy"])->name("delete");
});

// --- MENU MANAGEMENT (NEW) ---
Route::middleware(['auth', 'setLocale', 'user.active'])->prefix("menu")->name("menu.")->group(function () {
    Route::get("/index", [MenuController::class, "index"])->name("index");
    Route::post("/store", [MenuController::class, "store"])->name("store");
    Route::get("/{menu}/edit", [MenuController::class, "edit"])->name("edit");
    Route::post("/{menu}/update", [MenuController::class, "update"])->name("update");
    Route::delete("/{menu}/delete", [MenuController::class, "destroy"])->name("delete");
    Route::post('menu/{id}/update', [MenuController::class, 'update'])->name('admin.menu.update');
});

// --- SIGNATURE PLATTERS MANAGEMENT ---
Route::middleware(['auth', 'setLocale', 'user.active'])->prefix("signature-platters")->name("signature-platters.")->group(function () {
    Route::get("/index", [SignaturePlatterController::class, "index"])->name("index");
    Route::post("/store", [SignaturePlatterController::class, "store"])->name("store");
    Route::get("/{signaturePlatter}/edit", [SignaturePlatterController::class, "edit"])->name("edit");
    Route::post("/{signaturePlatter}/update", [SignaturePlatterController::class, "update"])->name("update");
    Route::delete("/{signaturePlatter}/delete", [SignaturePlatterController::class, "destroy"])->name("delete");
});

// --- FACEBOOK REELS MANAGEMENT ---
Route::middleware(['auth', 'setLocale', 'user.active'])->prefix("facebook-reels")->name("facebook-reels.")->group(function () {
    Route::get("/index", [FacebookReelController::class, "index"])->name("index");
    Route::post("/store", [FacebookReelController::class, "store"])->name("store");
    Route::get("/{facebookReel}/edit", [FacebookReelController::class, "edit"])->name("edit");
    Route::post("/{facebookReel}/update", [FacebookReelController::class, "update"])->name("update");
    Route::delete("/{facebookReel}/delete", [FacebookReelController::class, "destroy"])->name("delete");
});

// --- ABOUT SECTION MANAGEMENT ---
Route::middleware(['auth', 'setLocale', 'user.active'])->prefix("about")->name("about.")->group(function () {
    Route::get("/index", [AboutController::class, "index"])->name("index");
    Route::post("/store", [AboutController::class, "store"])->name("store");
});

// --- CONTACT SECTION MANAGEMENT ---
Route::middleware(['auth', 'setLocale', 'user.active'])->prefix("contact")->name("contact.")->group(function () {
    Route::get("/index", [ContactController::class, "index"])->name("index");
    Route::post("/store", [ContactController::class, "store"])->name("store");
});

// --- PARTY BOOKINGS MANAGEMENT ---
Route::middleware(['auth', 'setLocale', 'user.active'])->prefix("party-bookings")->name("party-bookings.")->group(function () {
    Route::get("/index", [\App\Http\Controllers\Backend\BookingController::class, "index"])->name("index");
    Route::post("/{booking}/update", [\App\Http\Controllers\Backend\BookingController::class, "update"])->name("update");
    Route::delete("/{booking}/delete", [\App\Http\Controllers\Backend\BookingController::class, "destroy"])->name("delete");
});

// --- COUPON MANAGEMENT ---
Route::middleware(['auth', 'setLocale', 'user.active'])->prefix("coupon")->name("coupon.")->group(function () {
    Route::get("/index", [CouponController::class, "index"])->name("index");
    Route::post("/store", [CouponController::class, "store"])->name("store");
    Route::get("/{coupon}/edit", [CouponController::class, "edit"])->name("edit");
    Route::post("/{coupon}/update", [CouponController::class, "update"])->name("update");
    Route::delete("/{coupon}/delete", [CouponController::class, "destroy"])->name("delete");
});