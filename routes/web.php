<?php

use App\Http\Controllers\Backend\MemberController;
use App\Http\Controllers\Backend\MenuController;
use App\Http\Controllers\Backend\OfferController;
use App\Http\Controllers\Backend\OrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\MemberAuthController;
use App\Http\Controllers\Frontend\PaymentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;


Auth::routes(['register' => true, 'verify' => true]);

// Route::get('/run-migrations', function (Request $request) {
//     if ($request->input('key') !== MIGRATION_KEY) {
//         abort(403, 'Unauthorized');
//     }
//     Artisan::call('migrate', ["--force" => true]);
//     Artisan::call('db:seed', ["--force" => true]);
//     return 'Migrations ran successfully';
// });

// Route::get('/linkstorage', function () {
//     Artisan::call('storage:link');
// });

Route::get('cache-clear', function () {
    try {
        Artisan::call('cache:clear');
        Artisan::call('optimize:clear');
        Cache::flush();
        Artisan::call('cache:forget spatie.permission.cache');
        return response()->json(['status' => 'success', 'msg' => 'Cache cleared successfully.'], 200);
    } catch (\Throwable $th) {
        //throw $th;
        return response()->json(['message' => $th->getMessage()], 500);
    }
})->name('cache.clear');



Route::middleware(['auth'])->group(function () {
    Route::get('pending-approval', function () {
        if (auth()->user()->status) {
            return redirect()->route('dashboard');
        }

        return view('auth.pending-approval');
    })->name('pending-approval');
});

Route::middleware(['auth', 'setLocale', 'user.active'])->group(function () {

    // System Settings Routes
    Route::get('settings', [SettingController::class, 'settings'])->name('system-setting');
    Route::post('customize', [SettingController::class, 'customize'])->name('theme.customize')->middleware('can:theme-customization');
    Route::get('general-setting', [SettingController::class, 'generalSetting'])->name('general-setting')->middleware('can:general-setting');
    Route::post('general-setting', [SettingController::class, 'generalSettingStore'])->name('general-setting.store')->middleware('can:general-setting');
    Route::post('logo-upload', [SettingController::class, 'logoUpload'])->name('general-setting-logo.store')->middleware('can:general-setting');
    Route::post('favicon-upload', [SettingController::class, 'faviconUpload'])->name('general-setting-favicon.store')->middleware('can:general-setting');
    Route::get('email-setting', [SettingController::class, 'emailSetting'])->name('email-setting')->middleware('can:email-setting');
    Route::post('email-setting', [SettingController::class, 'emailSettingUpdate'])->name('email-setting.store')->middleware('can:email-setting');
    Route::get('pusher-setting', [SettingController::class, 'pusherSetting'])->name('pusher-setting')->middleware('can:pusher-setting');
    Route::post('pusher-setting', [SettingController::class, 'pusherSettingStore'])->name('pusher-setting.store')->middleware('can:pusher-setting');
    Route::get('sslcommerz-setting', [SettingController::class, 'sslcommerzSetting'])->name('sslcommerz-setting')->middleware('can:general-setting');
    Route::post('sslcommerz-setting', [SettingController::class, 'sslcommerzSettingStore'])->name('sslcommerz-setting.store')->middleware('can:general-setting');
    Route::get('seo-setting', [SettingController::class, 'seoSetting'])->name('seo-setting')->middleware('can:general-setting');
    Route::post('seo-setting', [SettingController::class, 'seoSettingStore'])->name('seo-setting.store')->middleware('can:general-setting');
    Route::post('seo-og-image-upload', [SettingController::class, 'seoOgImageUpload'])->name('seo-setting-og-image.store')->middleware('can:general-setting');


    Route::middleware('role:Super Admin')->group(function () {
        // Role Route
        Route::resource('roles', RoleController::class);
        Route::get('roles/{role}/users', [RoleController::class, 'users'])->name('roles.users');
        Route::post('roles/user-remove', [RoleController::class, 'userRemove'])->name('roles.user.remove');
        Route::post('roles/user-add/{id}', [RoleController::class, 'userAdd'])->name('roles.user.add');
        Route::get('roles/{id}/permissions', [RoleController::class, 'rolePermission'])->name('roles.permissions');
        Route::post('roles/{id}/permissions', [RoleController::class, 'assignPermissions'])->name('roles.assignPermissions');

        // Permission Route
        Route::resource('permissions', PermissionController::class);
    });

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    # Notifications
    Route::controller(NotificationController::class)->prefix('notifications/')->name('notify.')->group(function () {
        Route::get('/', 'index')->name('index');
    });

    Route::resource('users', UserController::class)->except(['show']);

    Route::controller(ProfileController::class)->group(function () {
        Route::get('profile', 'profile')->name('profile');
        Route::put('profile', 'profileUpdate')->name('profile.update');
        Route::put('password', 'passwordUpdate')->name('password.update');
    });

    Route::get('members', [MemberController::class, 'index'])->name('members.index');
    Route::get('members/{member}', [MemberController::class, 'show'])->name('members.show');
    Route::post('members/{member}/toggle-status', [MemberController::class, 'toggleStatus'])->name('members.toggleStatus');
    Route::post('members/{member}/sync-purchase', [MemberController::class, 'syncPurchase'])->name('members.syncPurchase');
    Route::post('members/{member}/approve', [MemberController::class, 'approve'])->name('members.approve');
    Route::post('members/{member}/reject', [MemberController::class, 'reject'])->name('members.reject');
    Route::post('members/{member}/upgrade-golden', [MemberController::class, 'upgradeToGolden'])->name('members.upgradeGolden');
    // Admin menu management
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::post('admin/menu/{id}/toggle-popular', [MenuController::class, 'togglePopular'])->name('menu.togglePopular');
        Route::resource('menu', App\Http\Controllers\Backend\MenuController::class)->except(['show']);
        Route::post('menu/{menu}/delete', [App\Http\Controllers\Backend\MenuController::class, 'destroy'])->name('menu.delete');
        
    // Admin branch management
        Route::get('branch', [App\Http\Controllers\Backend\BranchController::class, 'index'])->name('branch.index');
        Route::post('branch', [App\Http\Controllers\Backend\BranchController::class, 'store'])->name('branch.store');
        Route::get('branch/{id}/edit', [App\Http\Controllers\Backend\BranchController::class, 'edit'])->name('branch.edit');
        Route::post('branch/{id}', [App\Http\Controllers\Backend\BranchController::class, 'update'])->name('branch.update');
        Route::delete('branch/{id}', [App\Http\Controllers\Backend\BranchController::class, 'destroy'])->name('branch.delete');

        // Admin review management
        Route::get('reviews', [App\Http\Controllers\Backend\ReviewController::class, 'index'])->name('reviews.index');
        Route::post('reviews/{review}/approve', [App\Http\Controllers\Backend\ReviewController::class, 'approve'])->name('reviews.approve');
        Route::post('reviews/{review}/reject', [App\Http\Controllers\Backend\ReviewController::class, 'reject'])->name('reviews.reject');
        Route::delete('reviews/{review}', [App\Http\Controllers\Backend\ReviewController::class, 'delete'])->name('reviews.delete');
    });
    // User account orders & invoices
    Route::get('account/orders', [App\Http\Controllers\Frontend\OrderController::class, 'index'])->name('account.orders');
    Route::get('account/orders/{order}', [App\Http\Controllers\Frontend\OrderController::class, 'invoice'])->name('account.invoice.show');
    Route::get('account/orders/{order}/download', [App\Http\Controllers\Frontend\OrderController::class, 'downloadInvoice'])->name('account.invoice.download');
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/latest-id', [OrderController::class, 'latestOrderId'])->name('orders.latestId');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // Offers CRUD
    Route::resource('offers', OfferController::class)->except(['show']);
    Route::post('offers/{offer}/toggle', [OfferController::class, 'toggleStatus'])->name('offers.toggle');
});

Route::name('frontend.')->group(function () {

    Route::get('/robots.txt', [App\Http\Controllers\Frontend\SeoController::class, 'robots'])->name('robots');
    Route::get('/sitemap.xml', [App\Http\Controllers\Frontend\SeoController::class, 'sitemap'])->name('sitemap');

    //* HOME PAGE
    Route::get('/', [HomeController::class, 'home'])->name('home');

    //* BRANCHES & MENU BY BRANCH
    Route::get('/branches', [App\Http\Controllers\Frontend\BranchesController::class, 'index'])->name('branches.index');
    Route::get('/branches/{branch:slug}', [App\Http\Controllers\Frontend\BranchesController::class, 'show'])->name('branches.show');
    Route::get('/branches/{branch:slug}/search-menu', [App\Http\Controllers\Frontend\BranchesController::class, 'searchMenu'])->name('branches.search-menu');

    //* OFFER CHECKING (for displaying offer badges on cards)
    Route::get('/api/variation/{id}/offers', [App\Http\Controllers\Frontend\OfferCheckController::class, 'getOffersForVariation'])->name('api.offers.for-variation');
    Route::get('/api/variations/with-offers', [App\Http\Controllers\Frontend\OfferCheckController::class, 'getAllVariationsWithOffers'])->name('api.offers.all-variations');
    Route::get('/add-to-cart', [HomeController::class, 'addToCart'])->name('addtocart');
    Route::get('/checkout', [HomeController::class, 'checkout'])->name('checkout');
    Route::get('/cards', [HomeController::class, 'cards'])->name('cards');
    Route::get('/card-apply', [HomeController::class, 'cardApply'])->name('card.apply');
    Route::post('/members/register', [HomeController::class, 'registerMember'])->name('members.register');
    Route::get('/member/check-phone', [HomeController::class, 'checkMemberPhone'])->name('members.check-phone');
    Route::post('/golden-card/apply', [HomeController::class, 'applyGoldenCard'])->name('golden.card.apply');
    Route::post('/order', [HomeController::class, 'storeOrder'])->name('order.store');
    Route::get('/member/check', [HomeController::class, 'checkMemberCard'])->name('member.check');
    Route::get('/menu', [HomeController::class, 'completeMenu'])->name('completeMenu');
    Route::get('/party-booking', [HomeController::class, 'partyBooking'])->name('partyBooking');
    Route::post('/party-booking', [HomeController::class, 'storePartyBooking'])->name('partyBooking.store');

    // Member login & dashboard (limited access)
    Route::get('/member/login', [MemberAuthController::class, 'showLoginForm'])->name('member.login');
    Route::post('/member/login', [MemberAuthController::class, 'login'])->name('member.login.submit');
    Route::post('/member/logout', [MemberAuthController::class, 'logout'])->name('member.logout');
    Route::get('/order-confirmation/{order}', [MemberAuthController::class, 'orderConfirmation'])->name('order.confirmation');
    Route::get('/track-order', [MemberAuthController::class, 'showTrackOrder'])->name('order.track');
    Route::post('/track-order', [MemberAuthController::class, 'trackOrder'])->name('order.track.submit');

    Route::middleware('auth:member')->group(function () {
        Route::get('/member/dashboard', [MemberAuthController::class, 'dashboard'])->name('member.dashboard');
    });

    //* REVIEWS
    Route::get('/reviews', [App\Http\Controllers\Frontend\ReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews', [App\Http\Controllers\Frontend\ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/reviews/verify-member', [App\Http\Controllers\Frontend\ReviewController::class, 'verifyMember'])->name('reviews.verify-member');
    Route::get('/contact', [App\Http\Controllers\Frontend\ReviewController::class, 'contact'])->name('contact');
});

Route::prefix('payment')->name('payment.')->group(function () {
    Route::match(['get', 'post'], '/success', [PaymentController::class, 'success'])->name('success');
    Route::match(['get', 'post'], '/fail', [PaymentController::class, 'fail'])->name('fail');
    Route::match(['get', 'post'], '/cancel', [PaymentController::class, 'cancel'])->name('cancel');
    Route::match(['get', 'post'], '/ipn', [PaymentController::class, 'ipn'])->name('ipn');
});
