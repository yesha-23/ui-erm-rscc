<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\KunjunganController;
use App\Http\Controllers\RekamMedisController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AkunController;
use App\Http\Controllers\DokumenController;

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

Route::prefix('rekam-medis/{noReg}')->name('rekam-medis.')->group(function () {
    Route::get('/', [RekamMedisController::class, 'show'])->name('show');

    Route::get('/dokumen/{jenis?}/{slug?}', [DokumenController::class, 'index'])
        ->whereIn('jenis', ['surat', 'persetujuan'])
        ->name('dokumen');
    Route::post('/dokumen/{jenis}/{slug}', [DokumenController::class, 'simpan'])
        ->whereIn('jenis', ['surat', 'persetujuan'])
        ->name('dokumen.simpan');
});

Route::prefix('akun')->name('akun.')->controller(AkunController::class)->group(function () {
    Route::get('/profil', 'profil')->name('profil');
    Route::get('/inbox/{id?}', 'inbox')->whereNumber('id')->name('inbox');
    Route::get('/pengaturan', 'pengaturan')->name('pengaturan');
    Route::post('/pengaturan/password', 'simpanPassword')->name('password');
    Route::post('/pengaturan/tampilan', 'simpanTampilan')->name('tampilan');
});