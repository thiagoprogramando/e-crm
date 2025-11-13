@extends('app.layout')
@section('content')

    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/front-page-pricing.css') }}"/>

    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <section class="pb-sm-12 pb-2 rounded-top">
                    <div class="container py-12">
                        <h2 class="text-center mb-2">Planos</h2>
                        <p class="text-center px-sm-12 mb-5">
                            Selecione o plano ideal para você!
                        </p>

                        <div class="pricing-plans row mx-4 gy-3 px-lg-12">
                            @foreach ($subscriptions as $key => $subscription)
                                <div class="col-lg-4 mb-lg-0 mb-3">
                                    <div class="card border shadow-none">
                                        <div class="card-body pt-12">
                                            <div class="mt-3 mb-5 text-center">
                                                <img src="{{ asset('assets/img/illustrations/subscription_'.($key + 1).'.png') }}" class="img-fluid" alt="CAPA DO PRODUTO"/>
                                            </div>
                                            <h4 class="card-title text-center text-capitalize mb-2">{{ $subscription->title }}</h4>
                                            <div class="text-center">
                                                <div class="d-flex justify-content-center">
                                                    <sup class="h6 pricing-currency mt-2 mb-0 me-1 text-body fw-normal">R$</sup>
                                                    <h1 class="mb-0 text-primary">{{ $subscription->value }}</h1>
                                                    <sub class="h6 pricing-duration mt-auto mb-1 text-body fw-normal">/{{ $subscription->timeLabel() }}</sub>
                                                </div>
                                            </div>
                                            <ul class="list-group ps-6 my-5 pt-4">
                                                <li class="mb-4">Gestão de Listas</li>
                                                <li class="mb-4">Envio de Nomes</li>
                                                <li class="mb-4">Acesso aos Produtos disponíveis</li>
                                                <li class="mb-4">Contratos Ilimitados</li>
                                                <li class="mb-0">Carteira Integrada</li>
                                            </ul>
                                            <form action="{{ route('created-subscription', ['uuid' => $subscription->uuid]) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-success d-grid w-100">Escolher Plano</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="buyModal{{ $subscription->uuid }}" tabindex="-1" aria-hidden="true">
                                    <form action="" method="POST">
                                        @csrf
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="exampleModalLabel1">Escolha uma forma de Pagamento</h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-12 col-sm-12 col-md-8 col-lg-8 mb-2">
                                                            <div class="form-floating form-floating-outline mb-2">
                                                                <select name="payment_method" id="payment_method_{{ $subscription->uuid }}" class="form-select" required>
                                                                    <option value="PIX">Pix</option>
                                                                    <option value="CREDIT_CARD">Cartão de Crédito</option>
                                                                </select>
                                                                <label for="payment_method_{{ $subscription->uuid }}">Forma de Pagamento</label>
                                                            </div>
                                                        </div>

                                                        <div class="col-12 col-sm-12 col-md-4 col-lg-4 mb-2">
                                                            <div class="form-floating form-floating-outline">
                                                                <select name="payment_installments" id="payment_installments_{{ $subscription->uuid }}" class="form-select" required></select>
                                                                <label for="payment_installments_{{ $subscription->uuid }}">Parcelas</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer btn-group">
                                                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-success">Avançar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/pages-pricing.js') }}"></script>
@endsection