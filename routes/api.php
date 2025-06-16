<?php

declare(strict_types=1);

use App\Http\Controllers\Api\ApiSearchController;
use Illuminate\Support\Facades\Route;

Route::post('/search', [ApiSearchController::class, 'post'])->name('api.search.post');
