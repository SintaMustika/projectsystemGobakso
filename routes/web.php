<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\MenuWebController;
use App\Http\Controllers\Web\IngredientWebController;
use App\Http\Controllers\Web\PosController;
use App\Http\Controllers\Web\ReportController;
use App\Http\Controllers\KitchenController;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
	Route::get('login', [AuthController::class, 'showLogin'])->name('login');
	Route::post('login', [AuthController::class, 'login']);
});

Route::post('logout', [AuthController::class, 'logout'])
	->name('logout')
	->middleware('auth');

/*
|--------------------------------------------------------------------------
| LANDING
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
	if (! auth()->check()) {
		return view('welcome');
	}

	$user = auth()->user();
	switch ($user->role) {
		case 'admin':
			return redirect()->route('admin.dashboard');
		case 'kasir':
			return redirect()->route('pos.index');
		case 'dapur':
			return redirect()->route('dapur.index');
		case 'owner':
			return redirect()->route('admin.dashboard');
		default:
			auth()->logout();
			request()->session()->invalidate();
			request()->session()->regenerateToken();
			return redirect()->route('login');
	}
})->name('home');

/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', [DashboardController::class, 'index'])
	->name('dashboard')
	->middleware(['auth','role:admin,kasir,owner']);

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth','role:admin'])->group(function () {

	Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

	// MENU
	Route::resource('menus', MenuWebController::class);

	Route::get('menus/{menu}/recipe', [MenuWebController::class, 'recipe'])->name('menus.recipe');
	Route::post('menus/{menu}/recipe', [MenuWebController::class, 'storeRecipe'])->name('menus.recipe.store');
	Route::put('menus/{menu}/recipe/{recipe}', [MenuWebController::class, 'updateRecipe'])->name('menus.recipe.update');
	Route::delete('menus/{menu}/recipe/{recipe}', [MenuWebController::class, 'deleteRecipe'])->name('menus.recipe.delete');

	// INGREDIENT
	Route::resource('ingredients', IngredientWebController::class);

	// ✅ REPORT (INI YANG PENTING)
	Route::get('reports/sales', [ReportController::class, 'index'])->name('reports.sales'); // <-- FIX
	Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');
});

/*
|--------------------------------------------------------------------------
| POS
|--------------------------------------------------------------------------
*/
Route::prefix('pos')->name('pos.')->middleware(['auth','role:admin,kasir'])->group(function () {

	Route::get('/', [PosController::class, 'index'])->name('index');
	Route::post('/', [PosController::class, 'store'])->name('store');

	Route::post('/checkout', [PosController::class, 'checkout'])->name('checkout');
	Route::get('/receipt/{order}', [PosController::class, 'receipt'])->name('receipt');

	Route::get('/{order}', [PosController::class, 'show'])->name('show');
	Route::post('/{order}/pay', [PosController::class, 'pay'])->name('pay');
});

/*
|--------------------------------------------------------------------------
| KASIR
|--------------------------------------------------------------------------
*/
Route::prefix('kasir')->name('kasir.')->middleware(['auth','role:kasir'])->group(function () {
	Route::get('pos', [PosController::class, 'index'])->name('pos');
});

/*
|--------------------------------------------------------------------------
| DAPUR (Kitchen)
|--------------------------------------------------------------------------
*/
Route::prefix('dapur')->name('dapur.')->middleware(['auth','role:dapur'])->group(function () {
	Route::get('/', [KitchenController::class, 'index'])->name('index');
	Route::post('{order}/process', [KitchenController::class, 'process'])->name('process');
	Route::post('{order}/complete', [KitchenController::class, 'complete'])->name('complete');
});