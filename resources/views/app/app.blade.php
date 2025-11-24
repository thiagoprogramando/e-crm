@extends('app.layout')
@section('content')

    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/cards-statistics.css') }}"/>

    <div class="col-12 col-sm-12 col-md-5 col-lg-5">
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title mb-1">Bem-vindo(a)! {{ Auth::user()->maskName() }}</h5>
                <p class="card-subtitle mb-3">
                    {{ \Carbon\Carbon::now()->locale('pt_BR')->isoFormat('dddd [às] HH:mm') }}
                </p>
                <h4 class="mb-0">
                    <a href="{{ route('wallet') }}" class="text-success">R$ {{ number_format(Auth::user()->wallet, 2, ',', '.') }}</a>
                </h4>
                <p class="mb-3">Cash-Back, Assinaturas, Bônus e muito mais! 😍</p>
                <button type="button" class="btn btn-sm btn-warning waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#createdModal">Enviar Cliente</button>
            </div>
            <img src="{{ asset('assets/img/illustrations/trophy.png') }}" class="scaleX-n1-rtl position-absolute bottom-0 end-0 me-4 mb-4 d-none d-md-block" height="162" alt="Bem-vindo(a)! {{ Auth::user()->maskName() }}">
        </div>

        @isset($list)
            <div class="card mb-3">
                <div class="card-body">
                    <blockquote class="blockquote mb-0">
                        <h3>
                            Lista: <strong>{{ $list->title }}</strong><br/>
                        </h3>
                        <footer class="blockquote-footer">
                            Data de envio: 
                            <cite title="{{ Carbon\Carbon::parse($list->date_end)->locale('pt_BR')->isoFormat('DD [de] MMMM [de] YYYY [às] HH:mm') }}"><strong>{{ Carbon\Carbon::parse($list->date_end)->locale('pt_BR')->isoFormat('DD [de] MMMM [de] YYYY [às] HH:mm') }}</strong></cite>
                        </footer>
                    </blockquote>
                </div>
            </div>
        @endisset
    
        {{-- <div class="card mb-3">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h5 class="mb-1">Notícias</h5>
                </div>
                <p class="mb-0 card-subtitle">Dados atualizados periodicamente.</p>
            </div>
            <div class="card-body">
                <div class="demo-inline-spacing mt-4">
                    <div class="list-group">
                        <div class="list-group-item list-group-item-action d-flex align-items-center cursor-pointer waves-effect">
                            <div class="w-100">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="user-info">
                                        <h6 class="mb-1 fw-normal">Lista 10/20 SERASA | SPC | Boa Vista OK</h6>
                                        <div class="d-flex align-items-center">
                                            <div class="user-status me-2 d-flex align-items-center">
                                                <span class="badge badge-dot bg-success me-1"></span>
                                                <small>Limpa Nome</small>
                                            </div>
                                            <div class="user-status me-2 d-flex align-items-center">
                                                <span class="badge badge-dot bg-info me-1"></span>
                                                <small>1000 e-mails</small>
                                            </div>
                                            <small class="text-muted ms-1">10/10/2025</small>
                                        </div>
                                    </div>
                                    <div class="add-btn">
                                        <button class="btn btn-primary btn-sm waves-effect waves-light">Acessar</button>
                                    </div>
                                </div>
                            </div>
                        </div>    
                    </div>
                </div>
            </div>
        </div> --}}
    </div>

    <div class="col-12 col-sm-12 col-md-7 col-lg-7">
        <div class="card bg-warning text-white mb-3 p-5">
            <figure class="mb-0">
                <blockquote class="blockquote">
                    <a onclick="onClip('{{ route('register', ['parent' => Auth::user()->uuid]) }}')">{{ route('register', ['parent' => Auth::user()->uuid]) }}</a>
                </blockquote>
                <figcaption class="blockquote-footer mb-0 text-white">
                    Link de indicação <cite title="para Auto Cadastro">para Auto Cadastro</cite>
                </figcaption>
            </figure>
        </div>
        
        <div class="card bg-warning mb-3">
            <div class="card-body pb-1 pt-0">
                <div class="mb-6 mt-1">
                    <div class="d-flex align-items-center">
                        <h1 class="mb-0 me-2 text-white">{{ $sales->count() }}</h1>
                        <div class="badge bg-label-dark rounded-pill">Dados atualizados automáticamente</div>
                    </div>
                    <p class="mt-0 text-white">Últimos envios</p>
                </div>
                <div class="table-responsive text-nowrap border-top">
                    <table class="table">
                        <tbody class="table-border-bottom-0">
                            <tr>
                                <td class="ps-0 py-4">
                                    <span class="text-white">CLIENTE</span>
                                </td>
                                <td class="ps-0">
                                    <span class="text-white">PROCESSO</span>
                                </td>
                            </tr>
                            @foreach ($sales as $sale)
                                <tr>
                                    <td class="ps-0 py-4">
                                        <span class="text-white">{{ Str::limit($sale->customer_name, 20) }}</span> <br>
                                        <span class="badge bg-dark me-1">
                                            <small class="text-white ms-1">{{ $sale->cpfcnpjLabel() }}</small>
                                        </span>
                                    </td>
                                    <td class="ps-0">
                                        <span class="text-white">
                                            {!! !empty($sale->list_id) ? $sale->statusLabel() .'</br> Lista '.$sale->list->title : 'PENDENTE DE PAGAMENTO' !!} <br>
                                            <span class="badge bg-dark me-1">
                                                <a onclick="onClip('{{ $sale->payment_url }}')" title="Acessar Fatura">Link de Pagamento</a>
                                            </span>
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="createdModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ route('created-sale') }}" method="POST" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="modalFullTitle">Nova Venda</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="form-floating form-floating-outline mb-2">
                                <div class="select2-primary">
                                    <select name="product_id" id="product_id" class="select2 form-select" required>
                                        <option value=" ">Escolha um Produto</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->uuid }}" data-addition="{{ Auth::user()->addition }}" data-options='@json($product->options)'>{{ $product->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <label for="product_id">Produto</label>
                            </div>
                        </div>

                        <div class="col-12 col-sm-12 col-md-7 col-lg-7">
                            <div class="form-floating form-floating-outline mb-2">
                                <input type="text" class="form-control" name="name" placeholder="Ex: João da Silva" required/>
                                <label>Nome <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-5 col-lg-5">
                            <div class="form-floating form-floating-outline mb-2">
                                <input type="text" class="form-control" name="cpfcnpj" oninput="maskCpfCnpj(this)" placeholder="000.000.000-00" required/>
                                <label for="cpfcnpj">CPF/CNPJ <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-7 col-lg-7">
                            <div class="form-floating form-floating-outline mb-2">
                                <input type="text" class="form-control" name="email" placeholder="Ex: joao@example.com"/>
                                <label for="email">E-mail</label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-5 col-lg-5">
                            <div class="form-floating form-floating-outline mb-2">
                                <input type="text" class="form-control" name="phone" oninput="maskPhone(this)" placeholder="(00) 90000-0000"/>
                                <label for="phone">Telefone</label>
                            </div>
                        </div>
                        
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="divider">
                                <div class="divider-text">Opções de Pagamento</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div id="product-options-container"></div>
                            <div class="row">
                                <div class="form-floating form-floating-outline mb-2">
                                    <div class="form-check mt-4">
                                        <input name="default-radio-1" class="form-check-input" type="radio" value="CLIENT" id="payment_customer_client" checked required>
                                        <label class="form-check-label" for="payment_customer_client">Pagamento com DADOS DO CLIENTE</label>
                                    </div>
                                    <div class="form-check mt-4">
                                        <input name="default-radio-1" class="form-check-input" type="radio" value="MY" id="payment_customer_my">
                                        <label class="form-check-label" for="payment_customer_my"> Pagamento com MEUS DADOS </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal"> Fechar </button>
                    <button type="submit" class="btn btn-success">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="{{ asset('assets/js/app.js') }}"></script>
@endsection