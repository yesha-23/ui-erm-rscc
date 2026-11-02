<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'no_hp'    => 'required',
            'password' => 'required',
            'captcha'  => 'required',
        ]);

        // TODO (IT RSCC): cek nomor HP, password, dan captcha ke database.
        // Untuk sekarang (tampilan saja) langsung masuk ke Beranda.
        return redirect()->route('beranda');
    }
}