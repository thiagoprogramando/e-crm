@extends('app.layout')
@section('content')

    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/cards-statistics.css') }}"/>
    <style>
        .masonry {
            column-count: 2;
            column-gap: 1rem;
        }

        .masonry-item {
            break-inside: avoid;
            margin-bottom: 1rem;
        }
    </style>

    <div class="col-12 col-sm-12 col-md-5 col-lg-5">

        <div class="kanban-add-new-board mb-5">
            @if (Auth::user()->type == 'master')
                <label class="kanban-add-board-btn" for="kanban-add-board-input" data-bs-toggle="modal" data-bs-target="#createdModal">
                    <i class="ri-filter-line"></i>
                    <span class="align-middle">Gerar Consulta (EndPoint)</span>
                </label>
            @endif
            <label class="kanban-add-board-btn" for="kanban-add-board-input" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="ri-filter-line"></i>
                <span class="align-middle">Filtrar</span>
            </label>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title mb-1">CARTEIRA DE CRÉDITOS</h5>
                <p class="card-subtitle mb-3">
                    {{ \Carbon\Carbon::now()->locale('pt_BR')->isoFormat('dddd [às] HH:mm') }}
                </p>
                <h4 class="text-success mb-0">
                    R$ {{ number_format(Auth::user()->wallet_cash, 2, ',', '.') }}
                </h4>
                <p class="mb-3">Os créditos podem levar até 10 minutos para serem processados.</p>
                <button type="button" class="btn btn-sm btn-warning waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#depositedModal">Depositar</button>
            </div>
        </div>

        <div class="card bg-warning mb-3">
            <div class="card-body pb-1 pt-0">
                <div class="mb-6 mt-1">
                    <div class="d-flex align-items-center">
                        <h1 class="mb-0 me-2 text-white">{{ $extracts->count() }}</h1>
                        <div class="badge bg-label-dark rounded-pill">Dados atualizados automáticamente</div>
                    </div>
                    <p class="mt-0 text-white">Histórico</p>
                </div>
                <div class="table-responsive text-nowrap border-top">
                    <table class="table">
                        <tbody class="table-border-bottom-0">
                            <tr>
                                <td class="ps-0 py-4">
                                    <span class="text-white">DETALHES</span>
                                </td>
                                <td class="ps-0 text-center">
                                    <span class="text-white">OPÇÕES</span>
                                </td>
                            </tr>
                            @foreach ($extracts as $extract)
                                <tr>
                                    <td class="ps-0 py-4">
                                        <span class="text-white">{{ $extract->cpfcnpjLabel() }}</span> <br>
                                        <small class="text-white ms-1">{{ $extract->search->title }}</small>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-dark btn-sm" title="Copiar URL da Consulta" onclick="onClip('{{ route('data-search', ['uuid' => $extract->uuid]) }}')"><i class="ri-file-copy-line"></i></button>
                                        <a href="{{ route('data-search', ['uuid' => $extract->uuid]) }}" target="_blank" class="btn btn-sm btn-info text-white" title="Comprovante">Acessar</a> 
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="text-center">
                        {{-- {{ $withdrawals->links() }} --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-12 col-md-7 col-lg-7">
        <div class="masonry">
            @foreach ($searchs as $search)
                <div class="masonry-item">
                    <div class="card card-border-shadow-warning">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <h5 class="mb-0">{{ $search->title }}</h5>
                            </div>
                            <p class="text-success">R$ {{ number_format($search->value + $search->addition, 2, ',', '.') }}</p>
                            <small class="text-muted">{{ $search->content }}</small> <br>

                            <div class="btn-group w-100 mt-2">
                                <button class="btn btn-sm btn-outline-warning mb-2" data-bs-toggle="modal" data-bs-target="#searchModal{{ $search->uuid }}" @disabled($search->status == 'inactive')>Consultar</button>
                                @if (Auth::user()->type == 'master')
                                    <button class="btn btn-sm btn-outline-info mb-2" data-bs-toggle="modal" data-bs-target="#updateModal{{ $search->uuid }}">Editar</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="searchModal{{ $search->uuid }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-sm" role="document">
                        <form action="{{ route('search', ['uuid' => $search->uuid]) }}" method="POST" class="modal-content">
                            @csrf
                            <div class="modal-header">
                                <h4 class="modal-title" id="modalFullTitle">Dados da Consulta</h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-danger alert-dismissible" role="alert">
                                    Atenção: CPF/CNPJ inválidos ou inexistentes serão consultados e descontados!
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                                <div class="row g-2">
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="form-floating form-floating-outline mb-2">
                                            <input type="text" class="form-control cpfcnpj" name="cpfcnpj" placeholder="CPF ou CNPJ" required/>
                                            <label for="cpfcnpj">CPF/CNPJ <span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer btn-group">
                                <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal"> Fechar </button>
                                <button type="submit" class="btn btn-success">Consultar</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="modal fade" id="updateModal{{ $search->uuid }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <form action="{{ route('updated-search', ['uuid' => $search->uuid]) }}" method="POST" enctype="multipart/form-data" class="modal-content">
                            @csrf
                            <div class="modal-header">
                                <h4 class="modal-title" id="modalFullTitle">Dados do Déposito</h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-2">
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="form-floating form-floating-outline mb-2">
                                            <input type="text" class="form-control" name="title" placeholder="SERASA CRENET PJ" value="{{ $search->title }}"/>
                                            <label>Título <span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="form-floating form-floating-outline mb-2">
                                            <input type="text" class="form-control" name="content" placeholder="Dados básicos" value="{{ $search->content }}"/>
                                            <label for="content">Descrição</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                        <div class="form-floating form-floating-outline mb-2">
                                            <input type="text" class="form-control money" name="value" placeholder="Ex: 100,00" oninput="maskValue(this)" value="{{ $search->value }}"/>
                                            <label for="value">Valor</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                        <div class="form-floating form-floating-outline mb-2">
                                            <input type="text" class="form-control money" name="addition" placeholder="Ex: 100,00" oninput="maskValue(this)" value="{{ $search->addition }}"/>
                                            <label for="addition">Adicional</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="form-floating form-floating-outline mb-2">
                                            <input type="text" class="form-control" name="api_url" placeholder="https://api.example.com" value="{{ $search->api_url }}"/>
                                            <label for="api_url">API URL</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                        <div class="form-floating form-floating-outline mb-2">
                                            <input type="text" class="form-control" name="api_token" placeholder="Seu token aqui" value="{{ $search->api_token }}"/>
                                            <label for="api_token">API TOKEN</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                        <div class="form-floating form-floating-outline mb-2">
                                            <input type="text" class="form-control" name="api_version" placeholder="Versão aqui" value="{{ $search->api_version }}"/>
                                            <label for="api_version">API VERSION</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                        <div class="form-floating form-floating-outline mb-2">
                                            <div class="select2-primary">
                                                <select name="api_method" id="api_method" class="select2 form-select">
                                                    <option value="  ">Métodos</option>
                                                    <option value="POST" @selected($search->api_method == 'POST')>POST</option>
                                                    <option value="GET" @selected($search->api_method == 'GET')>GET</option>
                                                    <option value="PUT" @selected($search->api_method == 'PUT')>PUT</option>
                                                    <option value="DELETE" @selected($search->api_method == 'DELETE')>DELETE</option>
                                                </select>
                                            </div>
                                            <label for="api_method">API MÉTODO</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-64 col-lg-6">
                                        <div class="form-floating form-floating-outline mb-2">
                                            <input type="text" class="form-control" name="api_code" placeholder="Código da API" value="{{ $search->api_code }}"/>
                                            <label for="api_code">API CODE</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer btn-group">
                                <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal"> Fechar </button>
                                <button type="submit" class="btn btn-success">Confirmar</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="modal fade" id="createdModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ route('created-search') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="modalFullTitle">Dados do Consulta</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="form-floating form-floating-outline mb-2">
                                <input type="text" class="form-control" name="title" placeholder="SERASA CRENET PJ"/>
                                <label>Título <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="form-floating form-floating-outline mb-2">
                                <input type="text" class="form-control" name="content" placeholder="Dados básicos"/>
                                <label for="content">Descrição</label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                            <div class="form-floating form-floating-outline mb-2">
                                <input type="text" class="form-control money" name="value" placeholder="Ex: 100,00" oninput="maskValue(this)" required/>
                                <label for="value">Valor</label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                            <div class="form-floating form-floating-outline mb-2">
                                <input type="text" class="form-control money" name="addition" placeholder="Ex: 100,00" oninput="maskValue(this)"/>
                                <label for="addition">Adicional</label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="form-floating form-floating-outline mb-2">
                                <input type="text" class="form-control" name="api_url" placeholder="https://api.example.com"/>
                                <label for="api_url">API URL</label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                            <div class="form-floating form-floating-outline mb-2">
                                <input type="text" class="form-control" name="api_token" placeholder="Seu token aqui"/>
                                <label for="api_token">API TOKEN</label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                            <div class="form-floating form-floating-outline mb-2">
                                <input type="text" class="form-control" name="api_version" placeholder="Versão aqui"/>
                                <label for="api_version">API VERSION</label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                            <div class="form-floating form-floating-outline mb-2">
                                <div class="select2-primary">
                                    <select name="api_method" id="api_method" class="select2 form-select">
                                        <option value="  ">Métodos</option>
                                        <option value="POST">POST</option>
                                        <option value="GET">GET</option>
                                        <option value="PUT">PUT</option>
                                        <option value="DELETE">DELETE</option>
                                    </select>
                                </div>
                                <label for="api_method">API MÉTODO</label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-64 col-lg-6">
                            <div class="form-floating form-floating-outline mb-2">
                                <input type="text" class="form-control" name="api_code" placeholder="Código da API"/>
                                <label for="api_code">API CODE</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer btn-group">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal"> Fechar </button>
                    <button type="submit" class="btn btn-success">Confirmar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <form action="{{ route('wallet') }}" method="GET" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="modalFullTitle">Dados da Pesquisa</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="form-floating form-floating-outline mb-2">
                                <input type="date" class="form-control cpfcnpj" name="payment_date_start"/>
                                <label for="payment_date_start">Data Inicial</label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="form-floating form-floating-outline mb-2">
                                <input type="date" class="form-control" name="payment_date_end"/>
                                <label for="payment_date_end">Data Final</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer btn-group">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal"> Fechar </button>
                    <button type="submit" class="btn btn-success">Confirmar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="depositedModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <form action="{{ route('created-deposit') }}" method="POST" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="modalFullTitle">Dados do Déposito</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="form-floating form-floating-outline mb-2">
                                <input type="text" class="form-control" name="name" value="{{ Auth::user()->name }}" readonly/>
                                <label>Nome <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="form-floating form-floating-outline mb-2">
                                <input type="text" class="form-control cpfcnpj" name="cpfcnpj" value="{{ Auth::user()->cpfcnpj }}" readonly/>
                                <label for="cpfcnpj">CPF/CNPJ <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="form-floating form-floating-outline mb-2">
                                <input type="text" class="form-control money" name="value" placeholder="Ex: 100,00" oninput="maskValue(this)" required/>
                                <label for="value">Valor</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer btn-group">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal"> Fechar </button>
                    <button type="submit" class="btn btn-success">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
@endsection