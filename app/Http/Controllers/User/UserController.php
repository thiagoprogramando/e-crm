<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller {

    public function index (Request $request) {
        
        $query = User::query();
        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->has('cpfcnpj')) {
            $query->where('cpfcnpj', preg_replace('/\D/', '', $request->input('cpfcnpj')));
        }

        if ($request->has('phone')) {
            $query->where('phone', preg_replace('/\D/', '', $request->input('phone')));
        }

        if ($request->has('email')) {
            $query->where('email', $request->input('email'));
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
        }

        if (Auth::user()->type !== 'admin') {
            $query->where('parent_id', Auth::id());
        } else {
            $affiliatedIds = Auth::user()->getDescendantIds();
            $query->whereIn('parent_id', array_merge([Auth::id()], $affiliatedIds));
        }

        return view('app.User.index', [
            'users' => $query->paginate(15),
        ]);
    }

    public function show ($uuid) {
        
        $user = User::where('uuid', $uuid)->first();
        if (!$user) {
            return redirect()->back()->with('error', 'Usuário não encontrado!');
        }

        return view('app.User.show', [
            'user' => $user,
        ]);
    }
    
    public function store (Request $request) {

        $request->merge([
            'cpfcnpj'   => preg_replace('/\D/', '', $request->cpfcnpj),
            'phone'     => preg_replace('/\D/', '', $request->phone),
        ]);

        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'cpfcnpj'   => 'required|string|max:20|unique:users',
            'phone'     => 'nullable|string|max:20',
        ], [
            'name.required'         => 'O campo nome é obrigatório.',
            'email.required'        => 'O campo e-mail é obrigatório.',
            'email.email'           => 'O campo e-mail deve ser um endereço de e-mail válido.',
            'email.unique'          => 'O e-mail informado já está em uso.',
            'cpfcnpj.required'      => 'O campo CPF/CNPJ é obrigatório.',
            'cpfcnpj.unique'        => 'O CPF/CNPJ informado já está em uso.',
        ]);
        
        $user               = new User();
        $user->uuid         = Str::uuid();
        $user->parent_id    = Auth::user()->id;
        $user->name         = $request->name;
        $user->email        = $request->email;
        $user->cpfcnpj      = preg_replace('/\D/', '', $request->cpfcnpj);
        $user->phone        = preg_replace('/\D/', '', $request->phone);
        $user->password     = bcrypt(preg_replace('/\D/', '', $request->cpfcnpj));
        $user->status       = $request->status;
        $user->type         = $request->type;
        if ($user->save()) {
            return redirect()->back()->with('success', 'Usuário cadastrado com sucesso!');
        } 

        return redirect()->back()->with('error', 'Erro ao cadastrar usuário, tente novamente!');
    }

    public function update (Request $request, $uuid) {

        $user = User::where('uuid', $uuid)->first();
        if (!$user) {
            return redirect()->back()->with('error', 'Usuário não encontrado!');
        }

        if (!empty($request->name)) {
            $user->name = $request->name;
        }
        if (!empty($request->email)) {
            $user->email = $request->email;
        }
        if (!empty($request->cpfcnpj)) {
            $user->cpfcnpj = preg_replace('/\D/', '', $request->cpfcnpj);
        }
        if (!empty($request->phone)) {
            $user->phone = preg_replace('/\D/', '', $request->phone);
        }
        if (!empty($request->type)) {
            $user->type = $request->type;
        }
        if (!empty($request->address)) {
            $user->address = $request->address;
        }
        if (!empty($request->status)) {
            $user->status = $request->status;
        }
        if (!empty($request->password)) {
            $user->password = bcrypt($request->password);
        }
        if (!empty($request->addition)) {
            $user->addition = $this->formatValue($request->addition);
        }

        if (!empty($request->photo)) {

            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            $file        = $request->file('photo');
            $filename    = Str::uuid() . '.' . $file->getClientOriginalExtension();

            $file->storeAs('profile-images', $filename, 'public');
            
            $user->photo = 'profile-images/' . $filename;
        }

        if ($user->save()) {
            return redirect()->back()->with('success', 'Dados atualizados com sucesso!');
        } 

        return redirect()->back()->with('error', 'Erro ao atualizar dados, tente novamente!');
    }

    public function destroy ($uuid) {

        $user = User::where('uuid', $uuid)->first();
        if ($user && $user->delete()) {
            return redirect()->back()->with('success', 'Usuário deletado com sucesso!');
        }

        return redirect()->back()->with('error', 'Erro ao deletar usuário, tente novamente!');
    }

    private function formatValue($valor) {
        
        $valor = preg_replace('/[^0-9,]/', '', $valor);
        $valor = str_replace(',', '.', $valor);
        $valorFloat = floatval($valor);
    
        return number_format($valorFloat, 2, '.', '');
    }
}
