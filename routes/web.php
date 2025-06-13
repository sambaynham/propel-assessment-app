<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AddressController;

Route::get('/', [AddressController::class, 'index'])->name('address.index');
Route::get('/create', [AddressController::class, 'create'])->name('address.create');
Route::post('/', [AddressController::class, 'post'])->name('address.post');
Route::get('/{id}', [AddressController::class, 'edit'])->name('address.edit');
Route::get('/{id}/delete', [AddressController::class, 'deleteConfirm'])->name('address.deleteConfirm');
Route::delete('/{id}', [AddressController::class, 'delete'])->name('address.delete');
Route::patch('/{id}', [AddressController::class, 'patch'])->name('address.patch');