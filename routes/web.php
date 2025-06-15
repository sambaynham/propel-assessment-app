<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AddressController;

Route::get('/', [AddressController::class, 'index'])->name('address.index');
Route::get('/address/create', [AddressController::class, 'create'])->name('address.create');
Route::post('/address/', [AddressController::class, 'post'])->name('address.post');
Route::get('/address/{id}', [AddressController::class, 'get'])->name('address.get');
Route::get('/address/{id}/edit', [AddressController::class, 'edit'])->name('address.edit');
Route::get('/address/{id}/delete', [AddressController::class, 'deleteConfirm'])->name('address.deleteConfirm');
Route::delete('/address/{id}', [AddressController::class, 'delete'])->name('address.delete');
Route::patch('/address/{id}', [AddressController::class, 'patch'])->name('address.patch');


Route::post('/search', [\App\Http\Controllers\SearchController::class, 'post'])->name('search.post');