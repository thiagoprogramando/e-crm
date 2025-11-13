@extends('app.layout')
@section('content')

    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
       
        <div class="btn-group mb-5" role="group" aria-label="First group">
            <button type="button" class="btn btn-outline-secondary waves-effect" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="tf-icons ri-filter-3-line"></i> Filtrar
            </button>
            <button type="button" class="btn btn-outline-secondary waves-effect" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="tf-icons ri-file-excel-2-line"></i> Importar
            </button>
        </div>
        
        <div class="card demo-inline-spacing">
            <div class="list-group p-0 m-0">
                @foreach ($sales as $sale)
                    <div class="list-group-item list-group-item-action d-flex align-items-center cursor-pointer waves-effect waves-light">
                        <div class="w-100">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="sale-info">
                                    <h6 class="mb-1 fw-normal">#{{ $sale->id }} {{ $sale->customer_name }}</h6>
                                    <div class="d-flex align-items-center">
                                        @isset($sale->list) 
                                            <div class="sale-status me-2 d-flex align-items-center">
                                                <span class="badge bg-dark">Lista {{ $sale->list->title }}</span>
                                            </div>
                                        @endisset
                                        <div class="sale-status me-2 d-flex align-items-center">
                                            {!! $sale->statusLabel() !!}
                                        </div>
                                        <div class="d-none d-md-flex">
                                            <div class="sale-status me-2 d-flex align-items-center" onclick="onClip('{{ $sale->payment_url }}')">
                                                <span class="badge bg-info me-1">{{ $sale->payment_url }}</span>
                                            </div>
                                            <div class="sale-status me-2 d-flex align-items-center" onclick="onClip('{{ $sale->customer_cpfcnpj }}')">
                                                <span class="badge bg-info me-1">{{ $sale->cpfcnpjLabel() }}</span>
                                            </div>
                                            <div class="sale-status me-2 d-flex align-items-center" onclick="onClip('{{ $sale->customer_phone }}')">
                                                <span class="badge bg-info me-1">{{ $sale->phoneLabel() }}</span>
                                            </div>
                                            <div class="sale-status me-2 d-flex align-items-center" onclick="onClip('{{ $sale->customer_email }}')">
                                                <span class="badge bg-info me-1">{{ $sale->customer_email }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <form action="{{ route('deleted-sale', ['uuid' => $sale->uuid]) }}" method="POST" class="add-btn delete">
                                    @csrf
                                    <button type="button" class="btn btn-success text-white btn-sm"  data-bs-toggle="modal" data-bs-target="#updatedModal{{ $sale->uuid }}" title="Editar Usuário"><i class="ri-menu-search-line"></i></button>
                                    <button type="submit" class="btn btn-danger btn-sm" title="Excluir Usuário"><i class="ri-delete-bin-line"></i></button>
                                </form>
                            </div>
                        </div>
                    </div> 

                    <div class="modal fade" id="updatedModal{{ $sale->uuid }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <form action="{{ route('updated-sale', ['uuid' => $sale->uuid]) }}" method="POST" enctype="multipart/form-data" class="modal-content">
                                @csrf
                                <div class="modal-header">
                                    <h4 class="modal-title" id="modalFullTitle">Detalhes da Venda</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row g-2">
                                        <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <input type="text" class="form-control" name="customer_name" placeholder="Ex: João da Silva" value="{{ $sale->customer_name }}"/>
                                                <label for="customer_name">Nome</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <input type="text" class="form-control cpfcnpj" name="customer_cpfcnpj" oninput="maskCpfCnpj(this)" placeholder="000.000.000-00" value="{{ $sale->customer_cpfcnpj }}"/>
                                                <label for="customer_cpfcnpj">CPF/CNPJ</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <input type="text" class="form-control" name="customer_email" placeholder="Ex: joao@example.com" value="{{ $sale->customer_email }}"/>
                                                <label for="customer_email">E-mail</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <input type="text" class="form-control phone" name="customer_phone" oninput="maskPhone(this)" placeholder="000.000.000-00" value="{{ $sale->customer_phone }}"/>
                                                <label for="phone">Telefone</label>
                                            </div>
                                        </div>
                                        @if (Auth::user()->type == 'admin')
                                            <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                                <div class="form-floating form-floating-outline mb-2">
                                                    <div class="select2-primary">
                                                        <select name="list_id" id="list_id" class="select2 form-select">
                                                            <option value="  ">Sem Lista</option>
                                                            @foreach ($lists as $list)
                                                                <option value="{{ $list->id }}" @selected($sale->list_id === $list->id)>{{ $list->title }}</option>
                                                            @endforeach    
                                                        </select>
                                                    </div>
                                                    <label for="list_id">Lista</label>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                                <div class="form-floating form-floating-outline mb-2">
                                                    <div class="select2-primary">
                                                        <select name="payment_status" id="payment_status" class="select2 form-select">
                                                            <option value="PENDING" @selected($sale->payment_status === 'PENDING')>Pendente</option>
                                                            <option value="PAID" @selected($sale->payment_status === 'PAID')>Aprovado</option>
                                                            <option value="CANCELED" @selected($sale->payment_status === 'CANCELED')>Cancelado</option>
                                                            <option value="REFUNDED" @selected($sale->payment_status === 'REFUNDED')>Reembolsado</option>
                                                            <option value="FAILED" @selected($sale->payment_status === 'FAILED')>Falhou</option>
                                                        </select>
                                                    </div>
                                                    <label for="payment_status">Status de Pagamento</label>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal"> Fechar </button>
                                    <button type="submit" class="btn btn-success">Confirmar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="card-footer">
                {{ $sales->links() }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ route('sales') }}" method="GET" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="modalFullTitle">Dados da Pesquisa</h4>
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
                                            <option value="{{ $product->id }}" data-options='@json($product->options)'>{{ $product->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <label for="product_id">Produto</label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-7 col-lg-7">
                            <div class="form-floating form-floating-outline mb-2">
                                <input type="text" class="form-control" name="name" placeholder="Ex: João da Silva"/>
                                <label>Nome</label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-5 col-lg-5">
                            <div class="form-floating form-floating-outline mb-2">
                                <input type="text" class="form-control" name="cpfcnpj" oninput="maskCpfCnpj(this)" placeholder="000.000.000-00"/>
                                <label for="cpfcnpj">CPF/CNPJ</label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                            <div class="form-floating form-floating-outline mb-2">
                                <div class="select2-primary">
                                    <select name="payment_status" id="payment_status" class="select2 form-select">
                                        <option value=" ">Todos</option>
                                        <option value="PENDING">Pendente</option>
                                        <option value="PAID">Aprovado</option>
                                        <option value="CANCELED">Cancelado</option>
                                        <option value="REFUNDED">Reembolsado</option>
                                        <option value="FAILED">Falhou</option>
                                    </select>
                                </div>
                                <label for="payment_status">Status de Pagamento</label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                            <div class="form-floating form-floating-outline mb-2">
                                <div class="select2-primary">
                                    <select name="list_id" id="list_id" class="select2 form-select" required>
                                        <option value=" ">Todas</option>
                                        @foreach ($lists as $list)
                                            <option value="{{ $list->id }}">{{ $list->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <label for="list_id">Lista</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal"> Fechar </button>
                    <button type="submit" class="btn btn-success">Pesquisar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ route('created-import-sale') }}" method="POST" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="modalFullTitle">Detalhes da Venda</h4>
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
                                            <option value="{{ $product->uuid }}" data-options='@json($product->options)'>{{ $product->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <label for="product_id">Produto</label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-2">
                            <p><strong class="text-danger">ATENÇÃO</strong></p>
                            <p>O Envio por Excel exige o preenchimento correto da <a href="{{ asset('assets/files/planilha_limpa_nome.xlsm') }}" download>PLANILHA MODELO</a>, os campos marcados com <b class="text-danger">*</b> são obrigatórios!</p>
                            <p>CPFs/CNPJs inválidos serão ignorados.</p>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="form-floating form-floating-outline mb-2">
                                <input type="file" class="form-control" name="file" placeholder="Nomes.xlsm" accept=".xlsm, .xlsx" required/>
                                <label for="file">Arquivo (Planilha preenchida) <span class="text-danger">*</span></label>
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
                                        <input name="payment_customer" class="form-check-input" type="radio" value="CLIENT" id="payment_customer_client" checked required>
                                        <label class="form-check-label" for="payment_customer_client">Pagamento com DADOS DO CLIENTE (Múltiplos Boletos)</label>
                                    </div>
                                    <div class="form-check mt-4">
                                        <input name="payment_customer" class="form-check-input" type="radio" value="MY" id="payment_customer_my">
                                        <label class="form-check-label" for="payment_customer_my"> Pagamento com MEUS DADOS (Único Boleto)</label>
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