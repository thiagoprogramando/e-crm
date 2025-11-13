@extends('layout')
    @section('content')
        <div class="position-relative">
            <div class="authentication-wrapper authentication-basic container-p-y p-4 p-sm-0">
                <div class="authentication-inner py-6">
                    <div class="card p-md-7 p-1">
                        <div class="app-brand justify-content-center mt-5 text-center">
                            <a href="" class="app-brand-link d-flex flex-column align-items-center gap-2">
                                <span>
                                    <img class="w-50" src="{{ asset('assets/img/logo.png') }}">
                                </span>
                            </a>
                        </div>                        
        
                        <div class="card-body mt-1 text-center">
                            <h4 class="mb-1 text-white">Esqueceu algo?</h4>
                            <p class="mb-5 text-white">Vamos recuperar o seu Acesso!</p>
                            
                            @if (empty($code))
                                <form id="formAuthentication" class="mb-5" action="{{ route('forgout-password') }}" method="POST">
                                    @csrf
                                    <div class="form-floating form-floating-outline mb-5">
                                        <input type="email" class="form-control" id="email" name="email" placeholder="E-mail:"/>
                                        <label for="email">Qual o seu E-mail de acesso?</label>
                                    </div>
                                    <div class="mb-5">
                                        <button class="btn btn-success d-grid w-100" type="submit">Recuperar Conta</button>
                                    </div>
                                </form>
                            @else
                                <form id="formAuthentication" class="mb-5" action="{{ route('recover-password', ['code' => $code]) }}" method="POST">
                                    @csrf
                                    <div class="form-floating form-floating-outline mb-2">
                                        <input type="text" class="form-control" id="password" name="password" placeholder="Escolha uma nova senha:"/>
                                        <label for="password">Escolha uma nova senha:</label>
                                    </div>
                                    <div class="form-floating form-floating-outline mb-2">
                                        <input type="password" class="form-control" id="password_confirmed" name="password_confirmed" placeholder="Confirme sua nova senha:"/>
                                        <label for="password_confirmed">Confirme sua nova senha:</label>
                                    </div>
                                    <div class="mb-5">
                                        <button class="btn btn-success d-grid w-100" type="submit">Confirmar Alteração</button>
                                    </div>
                                </form>
                            @endif
            
                            <p class="text-center text-white">
                                <a href="{{ route('login') }}" class="text-white">Acessar Minha Conta!</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

        