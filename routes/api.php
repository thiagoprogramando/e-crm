<?php

use App\Http\Controllers\Contract\ContractController;
use App\Http\Controllers\Gateway\AssasController;
use App\Http\Controllers\Gateway\CoraController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Webhooks
Route::post('webhook-assas', [AssasController::class, 'webhook'])->name('webhook-assas');
Route::post('webhook-cora', [CoraController::class, 'webhook'])->name('webhook-cora');

// Contract
Route::post('/updated-contract/{uuid}', [ContractController::class, 'update'])->name('updated-contract');