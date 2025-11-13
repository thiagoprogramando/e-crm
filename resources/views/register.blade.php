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
        
                        <div class="card-body mt-1">
                            <h4 class="mb-1 text-white text-center">Faça Parte! 👋</h4>
                            <p class="mb-5 text-white text-center">Complete seus dados para ter acesso aos benefícios da sua conta!</p>
                            @if ($errors->any())
                                @foreach ($errors->all() as $error)
                                    <div class="alert alert-danger alert-dismissible" role="alert">
                                        {!! $error !!}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endforeach
                            @endif
                            <form id="formAuthentication" class="mb-5" action="{{ route('registrer', ['parent' => $parent]) }}" method="POST">
                                @csrf
                                <div class="form-floating form-floating-outline mb-5">
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Nome:"/>
                                    <label for="name">Nome</label>
                                </div>
                                <div class="form-floating form-floating-outline mb-5">
                                    <input type="text" class="form-control" id="cpfcnpj" name="cpfcnpj" oninput="maskCpfCnpj(this)" placeholder="CPF ou CNPJ:"/>
                                    <label for="cpfcnpj">CPF ou CNPJ</label>
                                </div>
                                <div class="form-floating form-floating-outline mb-5">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="E-mail:"/>
                                    <label for="email">E-mail</label>
                                </div>
                                <div class="mb-5">
                                    <div class="form-password-toggle">
                                        <div class="input-group input-group-merge">
                                            <div class="form-floating form-floating-outline">
                                                <input type="password" id="password" class="form-control" name="password" placeholder="Senha:" aria-describedby="password"/>
                                                <label for="password">Senha</label>
                                            </div>
                                            <span class="input-group-text cursor-pointer">
                                                <i class="ri-eye-off-line"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-5">
                                    <button class="btn btn-warning d-grid w-100" type="submit">Avançar</button>
                                </div>
                            </form>

                            <p class="text-center">
                                <span>Já tem uma conta?</span> <a href="{{ route('login') }}" class="text-warning"><span>Faça login!</span></a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection