<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\KunjunganController;
use App\Http\Controllers\RekamMedisController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.proses');

Route::get('/beranda', BerandaController::class)->name('beranda');

Route::prefix('kunjungan')->name('kunjungan.')
    ->controller(KunjunganController::class)->group(function () {
        Route::get('/igd', 'igd')->name('igd');
        Route::get('/hemodialisa', 'hemodialisa')->name('hd');
        Route::get('/rehab-medik', 'rehabMedik')->name('rm');
        Route::get('/rawat-jalan', 'rawatJalan')->name('rajal');
        Route::get('/rawat-inap/{ruang?}', 'rawatInap')->whereIn('ruang', ['umum', 'kebidanan', 'icu', 'hcu'])->name('ranap');
        Route::get('/ok/{jenis?}', 'ok')->whereIn('jenis', ['mayor', 'minor'])->name('ok');
    });

Route::get('/rekam-medis/{noReg}', [RekamMedisController::class, 'show'])->name('rekam-medis.show');