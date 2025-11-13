<?php

namespace App\Http\Controllers\Access;

use App\Http\Controllers\Controller;

use App\Models\Plan;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RegisterController extends Controller {

    public function index($parent = null) {

        return view('register', [
            'parent' => $parent
        ]);
    }

    public function store(Request $request, $parent = null) {

        $request->cpfcnpj = preg_replace('/\D/', '', $request->cpfcnpj);

        $deletedUser = User::onlyTrashed()->where('email', $request->email)->orWhere('cpfcnpj', $request->cpfcnpj)->first();
        if ($deletedUser) {
            $deletedUser->restore();
            return redirect()->route('login')->with('success', 'Sua conta foi restaurada! Por favor, faça login para continuar!');
        }

        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users,email',
            'cpfcnpj'   => 'required|string|max:18',
        ], [
            'name.required'      => 'O nome é obrigatório!',
            'name.max'           => 'O nome não pode ter mais que 255 caracteres!',
            'email.required'     => 'O e-mail é obrigatório!',
            'email.email'        => 'Informe um e-mail válido!',
            'email.max'          => 'O e-mail não pode ter mais que 255 caracteres!',
            'email.unique'       => 'Este e-mail já está cadastrado!',
            'cpfcnpj.required'   => 'O CPF/CNPJ é obrigatório!',
            'cpfcnpj.max'        => 'O CPF/CNPJ não pode ter mais que 18 caracteres!',
        ]);

        $user                = new User();
        $user->uuid          = Str::uuid();
        $user->parent_id     = $parent ?? 1;
        $user->name          = $request->name;
        $user->email         = $request->email;
        $user->cpfcnpj       = preg_replace('/\D/', '', $request->cpfcnpj);
        $user->password      = bcrypt($request->password);
        if ($user->save()) {
            if (Auth::attempt(['email' => $user->email, 'password' => $request->password])) {
                return redirect()->route('app');
            } else {
                return redirect()->route('login')->with('success', 'Bem-vindo(a)! Faça Login para acessar os benefícios da sua conta!');
            }
        }

        return redirect()->back()->with('error', 'Erro ao cadastrar-se, verifique os dados e tente novamente!');
    }
    
}
