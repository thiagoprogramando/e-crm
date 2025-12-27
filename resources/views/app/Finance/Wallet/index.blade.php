@extends('app.layout')
@section('content')

    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/cards-statistics.css') }}"/>

    <div class="col-12 col-sm-12 col-md-6 col-lg-6">

        <div class="kanban-add-new-board mb-5">
            <label class="kanban-add-board-btn" for="kanban-add-board-input" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="ri-filter-line"></i>
                <span class="align-middle">Filtrar</span>
            </label>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title mb-1">Carteira de Comissões</h5>
                <p class="card-subtitle mb-3">
                    {{ \Carbon\Carbon::now()->locale('pt_BR')->isoFormat('dddd [às] HH:mm') }}
                </p>
                <h4 class="text-success mb-0">
                    R$ {{ number_format($balance, 2, ',', '.') }}
                </h4>
                <p class="mb-3">Saques são processados às 20h</p>
                <button type="button" class="btn btn-sm btn-warning waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#createdModal">Solicitar Saque</button>
            </div>
            <img src="{{ asset('assets/img/illustrations/subscription_3.png') }}" class="scaleX-n1-rtl position-absolute bottom-0 end-0 me-4 mb-4 d-none d-md-block" height="112" alt="Carteira de Comissões">
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <blockquote class="blockquote mb-0">
                    <h3>
                        <strong>Extrato</strong><br/>
                    </h3>
                    <footer class="blockquote-footer">
                        Os valores são somados automaticamente na sua carteira após confirmação de pagamento das vendas/produtos!<br/>
                    </footer>
                </blockquote>
                <div class="table-responsive text-nowrap border-top">
                    <table class="table">
                        <tbody class="table-border-bottom-0">
                            <tr>
                                <td class="ps-0 py-4">
                                    <span>DETALHES</span>
                                </td>
                                <td class="ps-0">
                                    <span>PROCESSAMENTO</span>
                                </td>
                            </tr>
                            @foreach ($commissions as $commission)
                                <tr>
                                    <td class="ps-0 py-4">
                                        <small>{{ $commission->description }}</small> <br>
                                        <span class="ms-1 text-success">R$ {{ number_format($commission->value, 2, ',', '.') }}</span>
                                    </td>
                                    <td class="ps-0">
                                        <span>
                                            {{ $commission->is_paid == true ? 'Pago' : 'Aguardando processamento...' }} <br>
                                            <small>{!! $commission->statusLabel() !!}</small>
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

    <div class="col-12 col-sm-12 col-md-6 col-lg-6">
        <div class="card bg-warning mb-3">
            <div class="card-body pb-1 pt-0">
                <div class="mb-6 mt-1">
                    <div class="d-flex align-items-center">
                        <h1 class="mb-0 me-2 text-white">{{ count($withdrawals) }}</h1>
                        <div class="badge bg-label-dark rounded-pill">Dados atualizados automáticamente</div>
                    </div>
                    <p class="mt-0 text-white">Saques</p>
                </div>
                <div class="table-responsive text-nowrap border-top">
                    <table class="table">
                        <tbody class="table-border-bottom-0">
                            <tr>
                                <td class="ps-0 py-4">
                                    <span class="text-white">DETALHES</span>
                                </td>
                                <td class="ps-0">
                                    <span class="text-white">PROCESSAMENTO</span>
                                </td>
                                <td class="ps-0 text-center">
                                    <span class="text-white">OPÇÕES</span>
                                </td>
                            </tr>
                            @foreach ($withdrawals as $withdrawal)
                                <tr>
                                    <td class="ps-0 py-4">
                                        <span class="text-white">{{ Str::limit($withdrawal->description ?? $withdrawal['description'], 30) }}</span> <br>
                                        <small class="text-white ms-1">R$ {{ number_format($withdrawal->value ?? $withdrawal['value'], 2, ',', '.') }}</small>
                                    </td>
                                    <td class="ps-0">
                                        <span class="text-white">
                                            {{ isset($withdrawal->is_paid) == true ? 'Pago' : 'Aguardando processamento...' }} <br>
                                            {{-- <small>{!! $withdrawal->statusLabel() ?? '' !!}</small> --}}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if(isset($withdrawal->is_paid) && $withdrawal->is_paid == false)
                                            <form action="{{ route('deleted-withdrawal', ['uuid' => $withdrawal->uuid]) }}" method="POST" class="confirm">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger"><i class="ri-close-circle-line"></i></button>
                                            </form>
                                        @else
                                            <a href="{{ isset($withdrawal->payment_url)  }}" target="_blank" class="btn btn-sm btn-success text-white" title="Comprovante"><i class="ri-file-list-3-line"></i></a>
                                        @endif
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
            <form action="{{ route('created-withdrawal') }}" method="POST" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="modalFullTitle">Dados da Transferência</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-12 col-sm-12 col-md-7 col-lg-7">
                            <div class="form-floating form-floating-outline mb-2">
                                <input type="text" class="form-control" name="payment_name" value="{{ Auth::user()->name }}" readonly/>
                                <label for="payment_name">Nome</label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-5 col-lg-5">
                            <div class="form-floating form-floating-outline mb-2">
                                <input type="text" class="form-control cpfcnpj" name="payment_document" value="{{ Auth::user()->cpfcnpj }}" readonly/>
                                <label for="payment_document">CPF/CNPJ</label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-7 col-lg-7">
                            <div class="form-floating form-floating-outline mb-2">
                                <input type="text" class="form-control cpfcnpj" name="payment_key" placeholder="CPF ou CNPJ" value="{{ Auth::user()->cpfcnpj }}" readonly/>
                                <label for="payment_key">Chave Pix (CPF/CNPJ)</label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-5 col-lg-5">
                            <div class="form-floating form-floating-outline mb-2">
                                <input type="text" class="form-control money" name="value" placeholder="Máx {{ number_format(Auth::user()->wallet, 2, ',', '.') }}" oninput="maskValue(this)" required/>
                                <label for="value">Valor</label>
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
@endsection