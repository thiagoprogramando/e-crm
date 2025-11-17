@extends('app.layout')
@section('content')

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/typography.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/katex.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/editor.css') }}"/>

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
                <div class="modal-dialog modal-lg" role="document">
                    <form action="{{ route('created-product') }}" method="POST" enctype="multipart/form-data" class="modal-content" id="formCreate">
                        @csrf
                        <div class="modal-header">
                            <h4 class="modal-title" id="modalFullTitle">Novo Produto</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-2">
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="form-floating form-floating-outline mb-2">
                                        <input type="text" class="form-control" name="title" placeholder="Ex: Rating Bancário" required/>
                                        <label>Título</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="full-editor">
                                        <h6>Contrato de Prestação de Serviço</h6>
                                        <p>
                                            De um lado, '@{{ CUSTOMER_NAME }}' denominada CONTRATANTE, e de outro lado, '@{{ COMPANY_NAME }}' denominada CONTRATADA.
                                        </p>
                                    </div>
                                    <textarea name="description" id="description" hidden></textarea>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                    <div class="form-floating form-floating-outline mb-2">
                                        <input type="text" class="form-control" name="value" oninput="maskValue(this)" placeholder="R$ 597,00"/>
                                        <label for="value">(R$) Valor Padrão</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                    <div class="form-floating form-floating-outline mb-2">
                                        <input type="text" class="form-control" name="cost_value" oninput="maskValue(this)" placeholder="R$ 297,00"/>
                                        <label for="cost_value">(R$) Custo de produção</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                    <div class="form-floating form-floating-outline mb-2">
                                        <input type="text" class="form-control" name="max_value" oninput="maskValue(this)" placeholder="R$ 997,00"/>
                                        <label for="max_value">(R$) Máximo de Venda</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                    <div class="form-floating form-floating-outline mb-2">
                                        <input type="text" class="form-control" name="min_value" oninput="maskValue(this)" placeholder="R$ 597,00"/>
                                        <label for="min_value">(R$) Mínimo de Venda</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                    <div class="form-floating form-floating-outline mb-2">
                                        <input type="text" class="form-control" name="cashback_value" oninput="maskValue(this)" placeholder="R$ 20,00"/>
                                        <label for="cashback_value">(R$) CashBack</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                    <div class="form-floating form-floating-outline mb-2">
                                        <input type="text" class="form-control" name="cashback_percentage" oninput="maskValue(this)" placeholder="50%"/>
                                        <label for="cashback_percentage">(%) CashBack</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                    <div class="form-floating form-floating-outline mb-2">
                                        <div class="select2-primary">
                                            <select name="status" id="status" class="select2 form-select">
                                                <option value="active">Ativo</option>
                                                <option value="inactive">Inativo</option>
                                            </select>
                                        </div>
                                        <label for="status">Status</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                    <div class="form-floating form-floating-outline mb-2">
                                        <div class="select2-primary">
                                            <select name="access" id="access" class="select2 form-select">
                                                <option value="  ">Todos</option>
                                                <option value="admin">Administradores</option>
                                                <option value="collaborator">Colaboradores</option>
                                                <option value="user">Consultores/Vendedores</option>
                                            </select>
                                        </div>
                                        <label for="ac">Acesso</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                    <div class="form-floating form-floating-outline mb-2">
                                        <div class="select2-primary">
                                            <select name="type" id="type" class="select2 form-select" required>
                                                @if (isset($type))
                                                    <option value="subscription">Assinatura/Planos</option>
                                                @endif
                                                <option value="product">Produto</option>
                                                <option value="service">Serviço</option>
                                            </select>
                                        </div>
                                        <label for="type">Tipo</label>
                                    </div>
                                </div>
                                @if (isset($type))
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                        <div class="form-floating form-floating-outline mb-2">
                                            <div class="select2-primary">
                                                <select name="time" id="time" class="select2 form-select" required>
                                                    <option value="monthly">Mensal</option>
                                                    <option value="semi-annually">Semestral</option>
                                                    <option value="yearly">Anual</option>
                                                    <option value="lifetime">Vitalício</option>
                                                </select>
                                            </div>
                                            <label for="time">Prazo</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="form-check form-check-primary mt-4">
                                            <input class="form-check-input" type="checkbox" name="is_blocked" value="true" id="is_blocked">
                                            <label class="form-check-label" for="is_blocked">Bloquear acesso ao encerrar assinatura</label>
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

            <div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <form action="{{ route('products', ['type' => $type]) }}" method="GET" class="modal-content">
                        @csrf
                        <div class="modal-header">
                            <h4 class="modal-title" id="modalFullTitle">Filtrar</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-2">
                                <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                                    <div class="form-floating form-floating-outline mb-2">
                                        <input type="text" class="form-control" name="title" placeholder="Ex: Como viajar para o Japão?"/>
                                        <label>Título</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                    <div class="form-floating form-floating-outline mb-2">
                                        <div class="select2-primary">
                                            <select name="status" id="status" class="select2 form-select" required>
                                                <option value="active">Ativo</option>
                                                <option value="inactive">Inativo</option>
                                            </select>
                                        </div>
                                        <label for="status">Status</label>
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
                @foreach ($products as $product)
                    <div class="list-group-item list-group-item-action d-flex align-items-center cursor-pointer waves-effect waves-light">
                        <div class="w-100">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="user-info">
                                    <h6 class="mb-1 fw-normal">{{ $product->title }}</h6>
                                    <div class="d-flex align-items-center">
                                        <div class="user-status me-2 d-flex align-items-center">
                                            {!! $product->statusLabel() !!}
                                        </div>
                                        @if (Auth::user()->type === 'admin')
                                            <div class="user-status me-2 d-flex align-items-center d-none d-md-flex">
                                                <span class="badge badge-dot bg-info me-1"></span>
                                                <small>Vendas: 0 |</small>
                                            </div>
                                            <div class="user-status me-2 d-flex align-items-center d-none d-md-flex">
                                                <span class="badge badge-dot bg-primary me-1"></span>
                                                <small>R$ {{ number_format($product->value, 2, ',', '.') }} |</small>
                                            </div>
                                            <div class="user-status me-2 d-flex align-items-center d-none d-md-flex">
                                                <span class="badge badge-dot bg-dark me-1"></span>
                                                <small>Acesso: {{ $product->accessLabel() }} </small>
                                            </div>
                                        @else
                                            <div class="user-status me-2 d-flex align-items-center d-none d-md-flex">
                                                <span class="badge badge-dot bg-info me-1"></span>
                                                <small>Vendas: 0 |</small>
                                            </div>
                                            <div class="user-status me-2 d-flex align-items-center d-none d-md-flex">
                                                <span class="badge badge-dot bg-primary me-1"></span>
                                                <small>R$ {{ number_format($product->value, 2, ',', '.') }} |</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <form action="{{ route('deleted-product', ['uuid' => $product->uuid]) }}" method="POST" class="btn-group delete">
                                    @csrf
                                    <button type="button" class="btn btn-outline-dark btn-sm"  data-bs-toggle="modal" data-bs-target="#detailsModal{{ $product->uuid }}" title="Detalhes do Produto"><i class="ri-eye-line"></i></button>
                                    @if (Auth::user()->type === 'admin')
                                        <button type="button" class="btn btn-outline-dark btn-sm"  data-bs-toggle="modal" data-bs-target="#updatedModal{{ $product->uuid }}" title="Editar Produto"><i class="ri-menu-search-line"></i></button>
                                        <button type="button" class="btn btn-outline-dark btn-sm"  data-bs-toggle="modal" data-bs-target="#optionModal{{ $product->uuid }}" title="Configurações do Produto"><i class="ri-hand-coin-line"></i></button>
                                        <button type="submit" class="btn btn-outline-dark btn-sm" title="Excluir Produto"><i class="ri-delete-bin-line"></i></button>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div> 

                    <div class="modal fade" id="updatedModal{{ $product->uuid }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <form action="{{ route('updated-product', ['uuid' => $product->uuid]) }}" method="POST" enctype="multipart/form-data" class="modal-content" data-id="{{ $product->uuid }}" id="formUpdate">
                                @csrf
                                <div class="modal-header">
                                    <h4 class="modal-title" id="modalFullTitle">Detalhes do Produto</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row g-2">
                                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <input type="text" class="form-control" name="title" placeholder="Ex: Rating Bancário" value="{{ $product->title }}"/>
                                                <label>Título</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                            <div class="full-editor" data-id="{{ $product->uuid }}">
                                                {!! $product->description !!}
                                            </div>
                                            <textarea name="description" id="description{{ $product->uuid }}" hidden></textarea>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <input type="text" class="form-control money" name="value" oninput="maskValue(this)" placeholder="R$ 597,00" value="{{ $product->value }}"/>
                                                <label for="value">(R$) Valor Padrão</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <input type="text" class="form-control money" name="cost_value" oninput="maskValue(this)" placeholder="R$ 297,00" value="{{ $product->cost_value }}"/>
                                                <label for="cost_value">(R$) Custo de produção</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <input type="text" class="form-control money" name="max_value" oninput="maskValue(this)" placeholder="R$ 997,00" value="{{ $product->max_value }}"/>
                                                <label for="max_value">(R$) Máximo de Venda</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <input type="text" class="form-control money" name="min_value" oninput="maskValue(this)" placeholder="R$ 597,00" value="{{ $product->min_value }}"/>
                                                <label for="min_value">(R$) Mínimo de Venda</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <input type="text" class="form-control money" name="fees_value" oninput="maskValue(this)" placeholder="R$ 57,00" value="{{ $product->fees_value }}"/>
                                                <label for="fees_value">(R$) Taxas (Paga pelo cliente)</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <input type="text" class="form-control money" name="cashback_value" oninput="maskValue(this)" placeholder="R$ 20,00" value="{{ $product->cashback_value }}"/>
                                                <label for="cashback_value">(R$) CashBack</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <input type="text" class="form-control performance" name="cashback_percentage" oninput="maskValue(this)" placeholder="50%" value="{{ $product->cashback_percentage }}"/>
                                                <label for="cashback_percentage">(%) CashBack</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <div class="select2-primary">
                                                    <select name="status" id="status" class="select2 form-select" required>
                                                        <option value="active" @selected($product->status == 'active')>Ativo</option>
                                                        <option value="inactive" @selected($product->status == 'inactive')>Inativo</option>
                                                    </select>
                                                </div>
                                                <label for="status">Status</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <div class="select2-primary">
                                                    <select name="access" id="access" class="select2 form-select">
                                                        <option value=" " selected>Todos</option>
                                                        <option value="admin" @selected($product->access == 'admin')>Administradores</option>
                                                        <option value="collaborator" @selected($product->access == 'collaborator')>Colaboradores</option>
                                                        <option value="user" @selected($product->access == 'user')>Consultores/Vendedores</option>
                                                    </select>
                                                </div>
                                                <label for="access">Acesso</label>
                                            </div>
                                        </div>
                                        @if (isset($type))
                                            <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                                <div class="form-floating form-floating-outline mb-2">
                                                    <div class="select2-primary">
                                                        <select name="time" id="time" class="select2 form-select">
                                                            <option value="monthly" @selected($product->time == 'monthly')>Mensal</option>
                                                            <option value="semi-annually" @selected($product->time == 'semi-annually')>Semestral</option>
                                                            <option value="yearly" @selected($product->time == 'yearly')>Anual</option>
                                                            <option value="lifetime" @selected($product->time == 'lifetime')>Vitalício</option>
                                                        </select>
                                                    </div>
                                                    <label for="time">Prazo</label>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                                <div class="form-check form-check-primary mt-4">
                                                    <input class="form-check-input" type="checkbox" name="is_blocked" value="true" id="is_blocked" @checked($product->is_blocked)>
                                                    <label class="form-check-label" for="is_blocked">Bloquear acesso ao encerrar assinatura</label>
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

                    <div class="modal fade" id="optionModal{{ $product->uuid }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="modalFullTitle">Detalhes de Pagamento</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="accordionPopoutIcon" class="accordion mt-4 accordion-popout">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header text-body d-flex justify-content-between" id="accordionPopoutIconOne">
                                                <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#accordionPopoutIcon-1" aria-controls="accordionPopoutIcon-1">
                                                    <i class="ri-add-large-line ri-20px me-2"></i>Adicionar Opção de Pagamento
                                                </button>
                                            </h2>

                                            <div id="accordionPopoutIcon-1" class="accordion-collapse collapse" data-bs-parent="#accordionPopoutIcon">
                                                <div class="accordion-body">
                                                    <form action="{{ route('created-payment-option', ['product' => $product->uuid]) }}" method="POST" class="row g-2">
                                                        @csrf
                                                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                                            <div class="form-floating form-floating-outline mb-2">
                                                                <input type="text" class="form-control" name="title" placeholder="Título" required/>
                                                                <label for="title">Título</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                                            <div class="form-floating form-floating-outline mb-2">
                                                                <textarea class="form-control h-px-100" name="description" id="description" placeholder="Ex: Comissão R$ 197"></textarea>
                                                                <label for="description">Descrição</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                                            <div class="form-floating form-floating-outline mb-2">
                                                                <input type="text" class="form-control money" name="value" oninput="maskValue(this)" placeholder="Valor" required/>
                                                                <label for="value">Valor</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                                            <div class="form-floating form-floating-outline mb-2">
                                                                <input type="text" class="form-control money" name="commission_seller" oninput="maskValue(this)" placeholder="Valor"/>
                                                                <label for="commission_seller">Comissão (R$ Fixa) Vendedor</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                                            <div class="form-floating form-floating-outline mb-2">
                                                                <input type="text" class="form-control money" name="commission_parent" oninput="maskValue(this)" placeholder="Valor"/>
                                                                <label for="commission_parent">Comissão (R$ Fixa) Indicador</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                                            <div class="form-floating form-floating-outline mb-2">
                                                                <textarea class="form-control h-px-100" name="payment_splits" id="payment_splits" placeholder="Splits (Json)"></textarea>
                                                                <label for="payment_splits">Splits (Json)</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                                            <div class="form-floating form-floating-outline mb-2">
                                                                <textarea class="form-control h-px-100" name="payment_settings" id="payment_settings" placeholder="Configurações (Json)"></textarea>
                                                                <label for="payment_settings">Configurações (Json)</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 text-end">
                                                            <button type="submit" class="btn btn-success">Adicionar Opção</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="accordion-item previous-active">
                                            <h2 class="accordion-header text-body d-flex justify-content-between" id="accordionPopoutIconTwo">
                                                <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#accordionPopoutIcon-2" aria-controls="accordionPopoutIcon-2">
                                                    <i class="ri-list-check-2 ri-20px me-2"></i>Opções de Pagamento
                                                </button>
                                            </h2>
                                            <div id="accordionPopoutIcon-2" class="accordion-collapse collapse show" data-bs-parent="#accordionPopoutIcon">
                                                <div class="accordion-body">
                                                    <div class="table-responsive text-nowrap">
                                                        <table class="table">
                                                            <tbody class="table-border-bottom-0">
                                                                @foreach ($product->options as $option)
                                                                    <tr>
                                                                        <td>
                                                                            <i class="ri-copper-coin-line ri-22px text-info me-4"></i><span class="fw-medium">{{ $option->title }}</span>
                                                                        </td>
                                                                        <td>{{ $option->description }}</td>
                                                                        <td><span class="badge rounded-pill bg-label-success me-1">R$ {{ number_format($option->value, 2, ',', '.') }}</span></td>
                                                                        <td>
                                                                            <form action="{{ route('deleted-payment-option', ['uuid' => $option->uuid]) }}" method="POST" class="add-btn">
                                                                                @csrf
                                                                                <button type="submit" class="btn btn-danger btn-sm" title="Excluir Opção"><i class="ri-delete-bin-line"></i></button>
                                                                            </form>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal"> Fechar </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="detailsModal{{ $product->uuid }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="modalFullTitle">Detalhes do Produto</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    {!! $product->description !!}
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal"> Fechar </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="card-footer">
                {{ $products->links() }}
            </div>
        </div>
    </div>

    <script src="https://cdn.tiny.cloud/1/tgezwiu6jalnw1mma8qnoanlxhumuabgmtavb8vap7357t22/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="{{ asset('assets/js/tinymce.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/quill/katex.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/quill/quill.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const fullToolbar = [
                [
                { font: [] },
                { size: [] }
                ],
                ['bold', 'italic', 'underline', 'strike'],
                [
                { color: [] },
                { background: [] }
                ],
                [
                { script: 'super' },
                { script: 'sub' }
                ],
                [
                { header: '1' },
                { header: '2' },
                'blockquote',
                'code-block'
                ],
                [
                { list: 'ordered' },
                { list: 'bullet' },
                { indent: '-1' },
                { indent: '+1' }
                ],
                [{ 'align': [] }],
                [{ direction: 'rtl' }],
                ['link', 'image', 'video', 'formula'],
                ['clean']
            ];

            const editors = document.querySelectorAll('.full-editor');
            const quills = {};

            editors.forEach((editor, index) => {
                const id = editor.getAttribute("data-id") ?? index;

                quills[id] = new Quill(editor, {
                    bounds: editor,
                    placeholder: 'Digite o conteúdo do contrato...',
                    modules: {
                        formula: true,
                        toolbar: fullToolbar
                    },
                    theme: 'snow'
                });
            });

            const create = document.getElementById('formCreate');
            if (create) {
                create.addEventListener('submit', function (event) {
                    event.preventDefault();

                    const firstQuillId = Object.keys(quills)[0];
                    const html = quills[firstQuillId].root.innerHTML.trim();

                    document.getElementById('description').value = html;

                    create.submit();
                });
            }

            document.querySelectorAll('form[data-id]').forEach(form => {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();

                    // UUID específico daquele form
                    const uuid = form.getAttribute('data-id');

                    // Pega o editor dentro SÓ deste form
                    const editorDiv = form.querySelector('.full-editor .ql-editor');

                    // Textarea correspondente
                    const textarea = document.getElementById('description' + uuid);

                    // Conteúdo HTML do Quill
                    const html = editorDiv.innerHTML.trim();

                    // Coloca no textarea
                    textarea.value = html;

                    // Agora envia de verdade
                    form.submit();
                });
            });
         });
    </script>
@endsection