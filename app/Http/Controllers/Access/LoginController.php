<?php

namespace App\Http\Controllers\Access;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller {
    
    public function index() {

        if (Auth::check()) {
            return redirect()->route('app');
        }

        return view('login');
    }

    public function store(Request $request) {

        $request->validate([
            'email'     => 'required|email|exists:users,email',
            'password'  => 'required',
        ], [
            'email.exists' => 'E-mail não pertence a nenhuma conta associada, verifique os dados e tente novamente!',
        ]);

        $credentials = $request->only(['email', 'password']);
        if (Auth::attempt($credentials)) {

            DB::table('sessions')->where('user_id', Auth::id())->delete();
            $request->session()->regenerate();

            return redirect()->route('app');
        } else {
            return redirect()->back()->withInput($request->only('email'))->with('error', 'Credenciais inválidas!');
        }
    }

    public function logout() {

        Auth::logout();
        return redirect()->route('login');
    }
}
