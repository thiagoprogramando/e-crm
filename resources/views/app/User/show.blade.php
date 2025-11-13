@extends('app.layout')
@section('content')

    <div class="col-12 col-sm-12 col-md-7 col-lg-7">
        <div class="card demo-inline-spacing">
            <div class="card-header align-items-center">
                <h5 class="card-action-title mb-0">Dados do Perfil</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('updated-user', ['uuid' => $user->uuid]) }}" method="POST" class="row">
                    @csrf
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 d-flex justify-content-center align-items-center flex-column text-center mb-3">
                        <img src="{{ Auth::user()->photo ? asset('storage/'.Auth::user()->photo) : asset('assets/img/avatars/man.png') }}" alt="Perfil de {{ Auth::user()->name }}" class="d-block w-px-100 h-px-100 rounded-4" id="change-photo-button" style="cursor: pointer;"/>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="form-floating form-floating-outline">
                            <input type="text" name="name" id="name" class="form-control" placeholder="Nome:" value="{{ $user->name }}"/>
                            <label for="name">Nome:</label>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-4 col-lg-4 mb-3">
                        <div class="form-floating form-floating-outline">
                            <input type="email" name="email" id="email" class="form-control" placeholder="E-mail:" value="{{ $user->email }}"/>
                            <label for="email">E-mail:</label>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-4 col-lg-4 mb-3">
                        <div class="form-floating form-floating-outline">
                            <input type="text" name="phone" id="phone" class="form-control phone" placeholder="Telefone:" value="{{ $user->phone }}" oninput="maskPhone(this)"/>
                            <label for="phone">Telefone:</label>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-4 col-lg-4 mb-3">
                        <div class="form-floating form-floating-outline">
                            <input type="text" name="cpfcnpj" id="cpfcnpj" class="form-control cpfcnpj" placeholder="CPF/CNPJ:" value="{{ $user->cpfcnpj }}" oninput="maskCpfCnpj(this)"/>
                            <label for="cpfcnpj">CPF/CNPJ:</label>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-3">
                        <div class="form-floating form-floating-outline">
                            <input type="text" name="address" id="address" class="form-control" placeholder="CEP:" value="{{ $user->address }}"/>
                            <label for="address">Endereço:</label>
                        </div>
                    </div>
                    
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 mb-3">
                        <div class="form-floating form-floating-outline">
                            <input type="text" name="password" id="password" class="form-control" placeholder="Nova senha:"/>
                            <label for="password">Nova senha:</label>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 mb-3">
                        <div class="form-floating form-floating-outline">
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirme a nova senha:"/>
                            <label for="confirm_password">Confirme a nova senha:</label>
                        </div>
                    </div>
                    @if (Auth::user()->type == 'admin')
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 mb-3">
                            <div class="form-floating form-floating-outline">
                                <div class="select2-primary">
                                    <select name="status" id="status" class="select2 form-select" required>
                                        <option value="active">Ativo</option>
                                        <option value="inactive">Inativo</option>
                                    </select>
                                </div>
                                <label for="status">Status</label>
                            </div>
                        </div>
                        <div class="col-6 col-sm-12 col-md-6 col-lg-6 mb-3">
                            <div class="form-floating form-floating-outline">
                                <div class="select2-primary">
                                    <select name="type" id="type" class="select2 form-select" required>
                                        <option value="admin" @selected($user->type == 'admin')>Administrador</option>
                                        <option value="collaborator" @selected($user->type == 'collaborator')>Colaborador</option>
                                        <option value="user" @selected($user->type == 'user')>Consultor</option>
                                    </select>
                                </div>
                                <label for="type">Permissões:</label>
                            </div>
                        </div>
                    @endif
                    <div class="col-6 col-sm-12 col-md-6 offset-md-6 col-lg-6 offset-lg-6 mb-3 d-flex justify-content-end align-items-center">
                        <button type="submit" class="btn btn-outline-success w-100 mt-2">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-12 col-md-5 col-lg-5">
        <div class="card card-action mb-6" id="invoices">
            <div class="card-header align-items-center">
                <h5 class="card-action-title mb-0">Faturas</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    @foreach ($user->invoices->sortBy('payment_status') as $invoice)
                        <li class="mb-4">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="me-2">
                                        <h6 class="mb-1">{{ $invoice->product->name ?? $invoice->title }} - R$ {{ number_format($invoice->value, 2, ',', '.') }}</h6>
                                        <small>{{ \Carbon\Carbon::parse($invoice->payment_date ?? $invoice->due_date)->format('d/m/Y') }}</small> | <small><a href="{{ $invoice->payment_url }}" target="_blank" rel="noopener noreferrer">Acessar</a></small>
                                    </div>
                                </div>
                                <div class="ms-auto">
                                    <a href="javascript:;">{!! $invoice->statusLabel() !!}</a>
                                </div>
                            </div>
                        </li>
                    @endforeach
                    
                    <li class="text-center">
                        <a href="javascript:;">Não há mais dados.</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <form action="{{ route('updated-user', ['uuid' => $user->uuid]) }}" method="POST" enctype="multipart/form-data" id="photo-upload-form" class="d-none">
        @csrf
        <input type="hidden" name="uuid" value="{{ $user->uuid }}">
        <input type="file" name="photo" id="photo-input" accept="image/*" onchange="document.getElementById('photo-upload-form').submit();">
    </form>

     <script>
        document.getElementById('change-photo-button').addEventListener('click', function() {
            document.getElementById('photo-input').click();
        });

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('input[type="file"]').forEach(input => {
                input.addEventListener("change", function() {
                    this.closest("form").submit();
                });
            });
        });
    </script>

@endsection