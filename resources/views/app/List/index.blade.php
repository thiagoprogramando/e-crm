@extends('app.layout')
@section('content')

    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        @if (Auth::user()->type === 'admin')
            <div class="kanban-add-new-board mb-5">
                <a class="kanban-add-board-btn" for="kanban-add-board-input" data-bs-toggle="modal" data-bs-target="#createdModal">
                    <i class="ri-add-line"></i>
                    <span class="align-middle">Adicionar</span>
                </a>
                <label class="kanban-add-board-btn" for="kanban-add-board-input" data-bs-toggle="modal" data-bs-target="#filterModal">
                    <i class="ri-filter-line"></i>
                    <span class="align-middle">Filtrar</span>
                </label>
            </div> 

            <div class="modal fade" id="createdModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <form action="{{ route('created-list') }}" method="POST" enctype="multipart/form-data" class="modal-content">
                        @csrf
                        <div class="modal-header">
                            <h4 class="modal-title" id="modalFullTitle">Nova Lista</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-2">
                                <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                                    <div class="form-floating form-floating-outline mb-2">
                                        <input type="text" class="form-control" name="title" placeholder="Ex: 15.09"/>
                                        <label>Título</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                    <div class="form-floating form-floating-outline mb-2">
                                        <div class="select2-primary">
                                            <select name="status" id="status" class="select2 form-select">
                                                <option value="active" selected>Ativo</option>
                                                <option value="inactive">Inativo</option>
                                            </select>
                                        </div>
                                        <label for="status">Status</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="form-floating form-floating-outline mb-2">
                                        <input type="text" class="form-control" name="description" placeholder="Ex: Última Lista do mês"/>
                                        <label for="description">Descrição</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                    <div class="form-floating form-floating-outline mb-2">
                                        <input type="datetime-local" class="form-control" name="date_start" placeholder="Ex: 15/09/2025"/>
                                        <label for="date_start">Data Inicial</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                    <div class="form-floating form-floating-outline mb-2">
                                        <input type="datetime-local" class="form-control" name="date_end" placeholder="Ex: 15/10/2025"/>
                                        <label for="date_end">Data Final</label>
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

            <div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <form action="{{ route('lists') }}" method="GET" class="modal-content">
                        @csrf
                        <div class="modal-header">
                            <h4 class="modal-title" id="modalFullTitle">Filtrar</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-2">
                                <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                                    <div class="form-floating form-floating-outline mb-2">
                                        <input type="text" class="form-control" name="title" placeholder="Ex: 15.09"/>
                                        <label>Título</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                    <div class="form-floating form-floating-outline mb-2">
                                        <div class="select2-primary">
                                            <select name="status" id="status" class="select2 form-select">
                                                <option value="  ">Todos</option>
                                                <option value="active">Ativo</option>
                                                <option value="inactive">Inativo</option>
                                            </select>
                                        </div>
                                        <label for="status">Status</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                    <div class="form-floating form-floating-outline mb-2">
                                        <input type="date" class="form-control" name="date_start" placeholder="Ex: 15/09/2025"/>
                                        <label for="date_start">Data Inicial</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                    <div class="form-floating form-floating-outline mb-2">
                                        <input type="date" class="form-control" name="date_end" placeholder="Ex: 15/10/2025"/>
                                        <label for="date_end">Data Final</label>
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
        @endif
        
        <div class="card demo-inline-spacing">
            <div class="list-group p-0 m-0">
                @foreach ($lists as $list)
                    <div class="list-group-item list-group-item-action flex-column align-items-start waves-effect">
                        <div class="d-flex justify-content-between w-100">
                            <h4 class="mb-1">{{ $list->title }}</h4>
                            <small>{{ \Carbon\Carbon::parse($list->date_start)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($list->date_end)->format('d/m/Y') }}</small>
                        </div>
                        <p class="mb-1">
                            <div class="d-none d-md-flex">
                                <div class="list-status me-2 d-flex align-items-center">
                                    <span class="badge badge-dot bg-success me-1"></span>
                                    {{ $list->statusLabel() }}
                                </div>
                                <div class="list-status me-2 d-flex align-items-center">
                                    <span class="badge badge-dot bg-info me-1"></span>
                                    <small>SERASA: {{ $list->serasaLabel() }}</small>
                                </div>
                                <div class="list-status me-2 d-flex align-items-center">
                                    <span class="badge badge-dot bg-info me-1"></span>
                                    <small>SPC: {{ $list->spcLabel() }}</small>
                                </div>
                                <div class="list-status me-2 d-flex align-items-center">
                                    <span class="badge badge-dot bg-info me-1"></span>
                                    <small>BOA VISTA: {{ $list->boaVistaLabel() }}</small>
                                </div>
                                <div class="list-status me-2 d-flex align-items-center">
                                    <span class="badge badge-dot bg-info me-1"></span>
                                    <small>CEPROT: {{ $list->ceprotLabel() }}</small>
                                </div>
                                <div class="list-status me-2 d-flex align-items-center">
                                    <span class="badge badge-dot bg-info me-1"></span>
                                    <small>BACEN: {{ $list->bacenLabel() }}</small>
                                </div>
                                <div class="list-status me-2 d-flex align-items-center">
                                    <span class="badge badge-dot bg-info me-1"></span>
                                    <small>RATING: {{ $list->ratingLabel() }}</small>
                                </div>
                                <div class="list-status me-2 d-flex align-items-center">
                                    <span class="badge badge-dot bg-info me-1"></span>
                                    <small>SCORE: {{ $list->scoreLabel() }}</small>
                                </div>
                            </div>
                        </p>
                        <form action="{{ route('deleted-list', ['uuid' => $list->uuid]) }}" method="POST" class="add-btn delete">
                            @csrf
                            <button type="button" class="btn btn-success text-white btn-sm"  data-bs-toggle="modal" data-bs-target="#exportModal{{ $list->uuid }}" title="Exportar Lista"><i class="ri-file-excel-2-line"></i></button>
                            @if (Auth::user()->type === 'admin')
                                <button type="button" class="btn btn-info text-white btn-sm"  data-bs-toggle="modal" data-bs-target="#updatedModal{{ $list->uuid }}" title="Editar Lista"><i class="ri-menu-search-line"></i></button>
                                <button type="submit" class="btn btn-danger btn-sm" title="Excluir Lista"><i class="ri-delete-bin-line"></i></button>
                            @endif
                        </form>
                    </div>

                    <div class="modal fade" id="updatedModal{{ $list->uuid }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <form action="{{ route('updated-list', ['uuid' => $list->uuid]) }}" method="POST" enctype="multipart/form-data" class="modal-content">
                                @csrf
                                <div class="modal-header">
                                    <h4 class="modal-title" id="modalFullTitle">Detalhes da Lista</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row g-2">
                                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <input type="text" class="form-control" name="title" placeholder="Ex: João da Silva" value="{{ $list->title }}"/>
                                                <label>Título:</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <input type="text" class="form-control" name="description" placeholder="Ex: Última lista do Ano" value="{{ $list->description }}"/>
                                                <label>Descrição</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <div class="select2-primary">
                                                    <select name="status" id="status" class="select2 form-select">
                                                        <option value="active" @selected($list->status == 'active')>Ativo</option>
                                                        <option value="inactive" @selected($list->status == 'inactive')>Inativo</option>
                                                    </select>
                                                </div>
                                                <label for="status">Status</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <div class="select2-primary">
                                                    <select name="status_serasa" id="status_serasa" class="select2 form-select">
                                                        <option value="pending" @selected($list->status_serasa == 'pending')>Pendente</option>
                                                        <option value="completed" @selected($list->status_serasa == 'completed')>Concluído</option>
                                                    </select>
                                                </div>
                                                <label for="status_serasa">Serasa</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <div class="select2-primary">
                                                    <select name="status_spc" id="status_spc" class="select2 form-select">
                                                        <option value="pending" @selected($list->status_spc == 'pending')>Pendente</option>
                                                        <option value="completed" @selected($list->status_spc == 'completed')>Concluído</option>
                                                    </select>
                                                </div>
                                                <label for="status_spc">SPC</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <div class="select2-primary">
                                                    <select name="status_boa_vista" id="status_boa_vista" class="select2 form-select">
                                                        <option value="pending" @selected($list->status_boa_vista == 'pending')>Pendente</option>
                                                        <option value="completed" @selected($list->status_boa_vista == 'completed')>Concluído</option>
                                                    </select>
                                                </div>
                                                <label for="status_boa_vista">Boa Vista</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <div class="select2-primary">
                                                    <select name="status_ceprot" id="status_ceprot" class="select2 form-select">
                                                        <option value="pending" @selected($list->status_ceprot == 'pending')>Pendente</option>
                                                        <option value="completed" @selected($list->status_ceprot == 'completed')>Concluído</option>
                                                    </select>
                                                </div>
                                                <label for="status_ceprot">Ceprot</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <div class="select2-primary">
                                                    <select name="status_bacen" id="status_bacen" class="select2 form-select">
                                                        <option value="pending" @selected($list->status_bacen == 'pending')>Pendente</option>
                                                        <option value="completed" @selected($list->status_bacen == 'completed')>Concluído</option>
                                                    </select>
                                                </div>
                                                <label for="status_bacen">Bacen</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <div class="select2-primary">
                                                    <select name="status_rating" id="status_rating" class="select2 form-select">
                                                        <option value="pending" @selected($list->status_rating == 'pending')>Pendente</option>
                                                        <option value="completed" @selected($list->status_rating == 'completed')>Concluído</option>
                                                    </select>
                                                </div>
                                                <label for="status_rating">Rating</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <div class="select2-primary">
                                                    <select name="status_score" id="status_score" class="select2 form-select">
                                                        <option value="pending" @selected($list->status_score == 'pending')>Pendente</option>
                                                        <option value="completed" @selected($list->status_score == 'completed')>Concluído</option>
                                                    </select>
                                                </div>
                                                <label for="status_score">Score</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <input type="datetime-local" class="form-control" name="date_start" placeholder="Ex: 15/09/2025" value="{{ $list->date_start }}"/>
                                                <label for="date_start">Data Inicial</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <input type="datetime-local" class="form-control" name="date_end" placeholder="Ex: 15/10/2025" value="{{ $list->date_end }}"/>
                                                <label for="date_end">Data Final</label>
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

                    <div class="modal fade" id="exportModal{{ $list->uuid }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <form action="{{ route('export-list', ['uuid' => $list->uuid]) }}" method="POST" enctype="multipart/form-data" class="modal-content">
                                @csrf
                                <div class="modal-header">
                                    <h4 class="modal-title" id="modalFullTitle">Configurações da Exportarção de Dados</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row g-2">
                                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <div class="select2-primary">
                                                    <select name="product_id" id="product_id" class="select2 form-select" required>
                                                        <option value=" ">Todos os Produtos</option>
                                                        @foreach ($products as $product)
                                                            <option value="{{ $product->id }}" data-options='@json($product->options)'>{{ $product->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <label for="product_id">Produto</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <div class="select2-primary">
                                                    <select name="payment_status" id="payment_status" class="select2 form-select">
                                                        <option value="  ">Todos os Status</option>
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
                                        <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <input type="date" class="form-control" name="date_start" placeholder="Ex: 15/09/2025"/>
                                                <label for="date_start">Data Inicial</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <input type="date" class="form-control" name="date_end" placeholder="Ex: 15/10/2025"/>
                                                <label for="date_end">Data Final</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal"> Fechar </button>
                                    <button type="submit" class="btn btn-success">Exportar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="card-footer">
                {{ $lists->links() }}
            </div>
        </div>
    </div>
@endsection