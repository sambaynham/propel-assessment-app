<?php

declare(strict_types=1);

use App\Http\Controllers\Visitor\AddressController;
use App\Http\Controllers\Visitor\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AddressController::class, 'index'])->name('visitor.address.index');
Route::get('/address/create', [AddressController::class, 'create'])->name('visitor.address.create');
Route::post('/address/', [AddressController::class, 'post'])->name('visitor.address.post');
Route::get('/address/{id}', [AddressController::class, 'get'])->name('visitor.address.get');
Route::get('/address/{id}/edit', [AddressController::class, 'edit'])->name('visitor.address.edit');
Route::get('/address/{id}/delete', [AddressController::class, 'deleteConfirm'])->name('visitor.address.deleteConfirm');
Route::delete('/address/{id}', [AddressController::class, 'delete'])->name('visitor.address.delete');
Route::patch('/address/{id}', [AddressController::class, 'patch'])->name('visitor.address.patch');
Route::post('/search', [SearchController::class, 'post'])->name('visitor.search.post');