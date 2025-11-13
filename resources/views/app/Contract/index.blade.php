@extends('app.layout')
@section('content')

    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
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
                <form action="{{ route('created-contract') }}" method="POST" enctype="multipart/form-data" class="modal-content">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title" id="modalFullTitle">Gerar Contrato</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-2">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="form-floating form-floating-outline mb-2">
                                    <div class="select2-primary">
                                        <select name="template_id" id="template_id" class="select2 form-select" required>
                                            <option value=" ">Escolha um Template</option>
                                            @foreach ($templates as $template)
                                                <option value="{{ $template->uuid }}">{{ $template->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <label for="template_id">Template</label>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="form-floating form-floating-outline mb-2">
                                    <div class="select2-primary">
                                        <select name="sale_id" id="sale_id" class="select2 form-select" required>
                                            <option value="  ">Escolha uma Venda</option>
                                            @foreach ($sales as $sale)
                                                <option value="{{ $sale->uuid }}">{{ '#'.$sale->id.' - '.$sale->customer_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <label for="sale_id">Venda</label>
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
                <form action="{{ route('contracts') }}" method="GET" class="modal-content">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title" id="modalFullTitle">Filtrar</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-2">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="form-floating form-floating-outline mb-2">
                                    <div class="select2-primary">
                                        <select name="template_id" id="template_id" class="select2 form-select" required>
                                            <option value=" ">Escolha um Template</option>
                                            @foreach ($templates as $template)
                                                <option value="{{ $template->id }}">{{ $template->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <label for="template_id">Template</label>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="form-floating form-floating-outline mb-2">
                                    <div class="select2-primary">
                                        <select name="sale_id" id="sale_id" class="select2 form-select" required>
                                            <option value="  ">Escolha uma Venda</option>
                                            @foreach ($sales as $sale)
                                                <option value="{{ $sale->id }}">{{ '#'.$sale->id.' - '.$sale->customer_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <label for="sale_id">Venda</label>
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
        
        <div class="card demo-inline-spacing">
            <div class="list-group p-0 m-0">
                @foreach ($contracts as $contract)
                    <div class="list-group-item list-group-item-action d-flex align-items-center cursor-pointer waves-effect waves-light">
                        <div class="w-100">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="user-info">
                                    <h6 class="mb-1 fw-normal">{{ '#'.$contract->id.' - '.$contract->sale->customer_name }}</h6>
                                    <div class="d-flex align-items-center">
                                        <div class="user-status me-2 d-flex align-items-center">
                                            {!! $contract->statusLabel() !!}
                                            @if($contract->signed_at)
                                                <span class="badge bg-info me-1">{{ \Carbon\Carbon::parse($contract->signed_at)->format('d/m/Y H:i:s') }}</span>
                                            @endif
                                            @if($contract->signed_ip)
                                                <span class="badge bg-info me-1" onclick="onClip('{{ $contract->signed_ip }}')">{{ $contract->signed_ip }}</span>
                                            @endif
                                            <span class="badge bg-dark me-1" onclick="onClip('{{ route('contract', ['uuid' => $contract->uuid]) }}')">{{ route('contract', ['uuid' => $contract->uuid]) }}</span>
                                        </div> 
                                    </div>
                                </div>
                                <form action="{{ route('deleted-contract', ['uuid' => $contract->uuid]) }}" method="POST" class="add-btn delete">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm" title="Excluir contract"><i class="ri-delete-bin-line"></i></button>
                                </form>
                            </div>
                        </div>
                    </div> 
                @endforeach
            </div>
            <div class="card-footer">
                {{ $contracts->links() }}
            </div>
        </div>
    </div>
@endsection