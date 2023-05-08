<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
 * ADMIN ROUTES
 */

use Illuminate\Support\Facades\Route;
use Modules\Catalogs\Http\Controllers\CatalogsController;
use Modules\Icons\Http\Controllers\IconsController;


/* Icons Admin */
Route::group(['prefix' => 'admin', 'middleware' => ['auth']], static function () {
    Route::group(['prefix' => 'icons'], static function () {
        Route::get('/', [IconsController::class, 'index'])->name('admin.icons.index');
        Route::get('/create/{path}', [IconsController::class, 'create'])->name('admin.icons.create');
        Route::post('/store', [IconsController::class, 'store'])->name('admin.icons.store');
        Route::get('to_many_pages', [IconsController::class, 'toManyPagesCreate'])->name('admin.icons.toManyPagesCreate');
        Route::post('/storeToManyPages', [IconsController::class, 'storeToManyPages'])->name('admin.icons.storeToManyPages');
        Route::post('/get-path', [IconsController::class, 'getEncryptedPath'])->name('admin.icons.manage.get-path');
        Route::get('/load-icons/{path}', [IconsController::class, 'loadIconsPage'])->name('admin.icons.manage.load-catalog');

        Route::group(['prefix' => 'multiple'], static function () {
            Route::get('active/{active}', [IconsController::class, 'activeMultiple'])->name('admin.icons.active-multiple');
            Route::get('delete', [IconsController::class, 'deleteMultiple'])->name('admin.icons.delete-multiple');
        });

        Route::group(['prefix' => '{id}'], static function () {
            Route::get('edit', [IconsController::class, 'edit'])->name('admin.icons.edit');
            Route::post('update', [IconsController::class, 'update'])->name('admin.icons.update');
            Route::get('delete', [IconsController::class, 'delete'])->name('admin.icons.delete');
            Route::get('show', [IconsController::class, 'show'])->name('admin.icons.show');
            Route::get('/active/{active}', [IconsController::class, 'active'])->name('admin.icons.changeStatus');
            Route::get('position/up', [IconsController::class, 'positionUp'])->name('admin.icons.position-up');
            Route::get('position/down', [IconsController::class, 'positionDown'])->name('admin.icons.position-down');
        });
    });

    /* Icon Set */
    Route::group(['prefix' => 'icon-sets'], static function () {
        Route::get('/', [IconsController::class, 'index'])->name('admin.icon-sets.index');
        Route::get('/create', [IconsController::class, 'create'])->name('admin.icon-sets.create');
        Route::post('/store', [IconsController::class, 'store'])->name('admin.icon-sets.store');

        Route::group(['prefix' => 'multiple'], static function () {
            Route::get('active/{active}', [IconsController::class, 'activeMultiple'])->name('admin.icon-sets.active-multiple');
            Route::get('delete', [IconsController::class, 'deleteMultiple'])->name('admin.icon-sets.delete-multiple');
        });

        Route::group(['prefix' => '{id}'], static function () {
            Route::get('edit', [IconsController::class, 'edit'])->name('admin.icon-sets.edit');
            Route::post('update', [IconsController::class, 'update'])->name('admin.icon-sets.update');
            Route::get('delete', [IconsController::class, 'delete'])->name('admin.icon-sets.delete');
            Route::get('show', [IconsController::class, 'show'])->name('admin.icon-sets.show');
            Route::get('/active/{active}', [IconsController::class, 'active'])->name('admin.icon-sets.changeStatus');
            Route::get('position/up', [IconsController::class, 'positionUp'])->name('admin.icon-sets.position-up');
            Route::get('position/down', [IconsController::class, 'positionDown'])->name('admin.icon-sets.position-down');
            Route::get('image/delete', [IconsController::class, 'deleteImage'])->name('admin.icon-sets.delete-image');
        });
    });
    Route::get('/{parentTypeId}/{parentId}/create', [IconsController::class, 'create']);
    Route::post('/{parentTypeId}/{parentId}/store', [IconsController::class, 'store']);
    Route::get('/{parentTypeId}/{parentId}/positions', [IconsController::class, 'positions']);
    Route::get('/{parentTypeId}/{parentId}/{id}/edit', [IconsController::class, 'edit']);
    Route::get('/loadIcons/{parentTypeId}/{parentId}', [IconsController::class, 'galleries']);
    Route::get('/importFromIconSet/{parentTypeId}/{parentId}', [IconsController::class, 'importFromIconSet']);
    Route::get('/importFromIconSetForGrandAdmin/{parentTypeId}/{parentId}', [IconsController::class, 'importFromIconSetForGrandAdmin']);
    Route::get('/importFromIconSet/download/iconSet/{setId}', [IconsController::class, 'downloadIconSet']);
    Route::get('/importFromIconSet/load/iconSet/{setId}', [IconsController::class, 'loadLocallyIconSet']);
    Route::post('/importFromIconSet/{parentTypeId}/{parentId}/store', [IconsController::class, 'importFromIconSetStore']);
});
