<!doctype html>

<html lang="pt-br" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-template="horizontal-menu-template-no-customizer" data-style="light">
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>
        <title>{{ env('APP_NAME') }} | {{ env('APP_DESCRIPTION') }}</title>
        <meta name="description" content="{{ env('APP_DESCRIPTION') }}"/>

        <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon.png') }}"/>
        <link rel="preconnect" href="https://fonts.googleapis.com"/>
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap" rel="stylesheet"/>

        <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/remixicon/remixicon.css') }}"/>
        <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/flag-icons.css') }}"/>

        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}"/>

        <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core-dark.css') }}"/>
        <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default-dark.css') }}"/>
        <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}"/>
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/typeahead-js/typeahead.css') }}"/>

        <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
        <script src="{{ asset('assets/js/config.js') }}"></script>
        <style>
            body {
                margin: 20px;
                padding: 20px;

                background-color: #f8f9fa;
                font-size: 14px;
            }

            .floating-button {
                position: fixed;
                bottom: 30px;
                left: 50%;
                transform: translateX(-50%);
                background-color: #007bff;
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s ease-in-out;
                z-index: 9999;
            }

            @media print {
                .floating-button {
                    display: none !important;
                }

                #print-area, #print-area * {
                    visibility: visible;
                }
            }
        </style>
    </head>
    <body>
        <div id="print-area" class="col-12 col-sm-12 col-md-12 col-lg-12">
            <div class="row">
                <div class="card bg-primary mb-3">
                    <div class="card-header text-center">
                        <h5 class="card-title mb-0">{{ $search->title }}</h5>
                        <hr>
                        <small class="text-success">{{ $data->created_at->format('d/m/Y') }} |  {{ $data->cpfcnpjLabel() }}</small>
                    </div>
                    <div class="card-body">
                        @if(!empty($pessoa))
                            <p>IDENTIFICAÇÃO PESSOAL</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Nome:</strong> {{ $pessoa['NOME'] ?? '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>CNPJ / CPF:</strong> {{ $pessoa['NUMERO_DOC'] ?? '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Tipo Pessoa:</strong> {{ $pessoa['TIPO_PESSOA'] ?? '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Situação Jurídica:</strong> {{ $pessoa['SITUACAO_JURIDICA'] ?? '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Data Abertura/Nascimento:</strong> {{ $pessoa['DATA_ABERTURA'] ?? '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Natureza Jurídica:</strong> {{ $pessoa['NATUREZA_JURIDICA'] ?? '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Telefone:</strong> {{ $pessoa['TELEFONE'] ?? '-' }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card mb-2">
                    <div class="card-body">
                        @if(!empty($pendencias))
                            <h3>PENDÊNCIAS FINANCEIRAS</h3>
                            <p><strong>Total de Ocorrências:</strong> {{ $pendencias['quantidade'] }}</p>
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>Data Venc.</th>
                                            <th>Valor</th>
                                            <th>Credor</th>
                                            <th>Contrato</th>
                                            <th>Modalidade</th>
                                            <th>Informante</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pendencias['ocorrencias'] as $o)
                                            <tr>
                                                <td>{{ $o['data_vencimento'] }}</td>
                                                <td>R$ {{ $o['valor'] }}</td>
                                                <td>{{ $o['credor'] }}</td>
                                                <td>{{ $o['contrato'] }}</td>
                                                <td>{{ $o['modalidade'] }}</td>
                                                <td>{{ $o['informante'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <hr>
                            </div>

                            @if(!empty($passagens))
                                <h3>Histórico de consultas</h3>
                                <p><strong>Total de Ocorrências:</strong> {{ $passagens['quantidade'] }}</p>
                                <div class="table-responsive mb-3">
                                    <table class="table table-bordered table-sm">
                                        <thead>
                                            <tr>
                                                <th>Data da Consulta</th>
                                                <th>Cliente</th>
                                                <th>Telefone</th>
                                                <th>Cidade/UF</th>
                                            </tr>
                                        </thead>
                                            <tbody>
                                                @foreach ($passagens['ocorrencias'] as $p)
                                                    <tr>
                                                        <td>{{ $p['data_consulta'] }}</td>
                                                        <td>{{ $p['cliente'] }}</td>
                                                        <td>{{ $p['telefone'] }}</td>
                                                        <td>{{ $p['cidade_uf'] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                    </table>
                                    <hr>
                                </div>
                            @endif
                        @endif

                        @if (!empty($protestoAnalitico) || !empty($protestoSintetico)) 
                            <h3>Protestos em cartório.</h3>
                        @endif
                        @if (!empty($protestoAnalitico))
                            <p><strong>Total de Ocorrências:</strong> {{ $protestoAnalitico['quantidade'] }}</p>

                            @if (!empty($protestoAnalitico['valor_total']))
                                <p><strong>Valor Total:</strong> {{ $protestoAnalitico['valor_total'] }}</p>
                            @endif

                            @if (!empty($protestoAnalitico['ultimo']))
                                <p><strong>Último Protesto:</strong> {{ $protestoAnalitico['ultimo'] }}</p>
                            @endif

                            <div class="table-responsive mb-3">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Valor</th>
                                            <th>Origem</th>
                                            <th>Credor</th>
                                            <th>Tipo Anotação</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($protestoAnalitico['ocorrencias'] as $p)
                                            <tr>
                                                <td>{{ $p['data'] }}</td>
                                                <td>R$ {{ $p['valor'] }}</td>
                                                <td>{{ $p['origem'] }}</td>
                                                <td>{{ $p['credor'] }}</td>
                                                <td>{{ $p['tipo'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <hr>
                            </div>
                        @endif

                        @if (!empty($protestoSintetico))
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>UF</th>
                                            <th>Telefone</th>
                                            <th>Endereço</th>
                                            <th>Cartório</th>
                                            <th>Comarca</th>
                                            <th>Info Cartórios</th>
                                            <th>Data Ocorrência</th>
                                            <th>Atualização</th>
                                            <th>Valor Protesto</th>
                                            <th>Credor</th>
                                            <th>Cedente</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($protestoSintetico['ocorrencias'] as $p)
                                            <tr>
                                                <td>{{ $p['uf'] }}</td>
                                                <td>{{ $p['telefone'] }}</td>
                                                <td>{{ $p['endereco'] }}</td>
                                                <td>{{ $p['cartorio'] }}</td>
                                                <td>{{ $p['comarca'] }}</td>
                                                <td>{{ $p['info'] }}</td>
                                                <td>{{ $p['data'] }}</td>
                                                <td>{{ $p['atualizado'] }}</td>
                                                <td>{{ $p['valor'] }}</td>
                                                <td>{{ $p['credor'] }}</td>
                                                <td>{{ $p['cedente'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <hr>
                            </div>
                        @endif

                        @if (!empty($chequesSemFundo))
                            <h3>Cheques sem fundo - BACEN.</h3>
                            <p><strong>Total de Ocorrências:</strong> {{ $chequesSemFundo['quantidade'] }}</p>
                            @if (!empty($chequesSemFundo['correntista']))
                                <p><strong>Correntista:</strong> {{ $chequesSemFundo['correntista'] }}</p>
                            @endif
                            @if (!empty($chequesSemFundo['documento']))
                                <p><strong>Documento:</strong> {{ $chequesSemFundo['documento'] }}</p>
                            @endif
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>Banco</th>
                                            <th>Agência</th>
                                            <th>Motivo Devolução</th>
                                            <th>Qtd. Cheques</th>
                                            <th>Data Última Ocorrência</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($chequesSemFundo['ocorrencias'] as $p)
                                            <tr>
                                                <td>{{ $p['banco'] }}</td>
                                                <td>{{ $p['agencia'] }}</td>
                                                <td>{{ $p['motivo'] }}</td>
                                                <td>{{ $p['qtd_cheques'] }}</td>
                                                <td>{{ $p['data'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <hr>
                            </div>
                        @endif

                        @if (!empty($contumacia))
                            <h3>Contumácia - Registro de sustações de cheques.</h3>
                            <p><strong>Total de Ocorrências:</strong> {{ $contumacia['quantidade'] }}</p>
                            @if (!empty($contumacia['data_primeira']))
                                <p><strong>Primeira Ocorrência:</strong> {{ $contumacia['data_primeira'] }}</p>
                            @endif
                            @if (!empty($contumacia['data_ultima']))
                                <p><strong>Última Ocorrência:</strong> {{ $contumacia['data_ultima'] }}</p>
                            @endif
                            <hr>
                        @endif

                        @if (!empty($score))
                            <h3>Score de Crédito.</h3>
                            <p><strong>Total de Ocorrências:</strong> {{ $score['quantidade'] }}</p>
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>Tipo</th>
                                            <th>Score</th>
                                            <th>Classificação ABC</th>
                                            <th>Probabilidade Inadimplência (%)</th>
                                            <th>Descrição</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($score['ocorrencias'] as $s)
                                            <tr>
                                                <td>{{ $s['tipo'] }}</td>
                                                <td>{{ intval($s['valor']) }}</td>
                                                <td>{{ $s['classificacao_abc'] }}</td>
                                                <td>{{ $s['probabilidade'] }}</td>
                                                <td>{{ $s['risco_texto'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <hr>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <button type="button" onclick="window.print()" class="floating-button btn btn-primary" media="screen">
            Imprimir
        </button>

        <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
        <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/i18n/i18n.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
        <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
        <script src="{{ asset('assets/js/main.js') }}"></script>
        <script src="{{ asset('assets/js/mask.js') }}"></script>
        <script src="{{ asset('assets/js/sweetalert.js') }}"></script>
        <script>
            @if(session('error'))
                Swal.fire({
                    title: 'Erro!',
                    text: '{{ session('error') }}',
                    icon: 'error',
                    timer: 2000
                })
            @endif

            @if(session('infor'))
                Swal.fire({
                    title: 'Atenção!',
                    text: '{{ session('infor') }}',
                    icon: 'info',
                    timer: 2000
                })
            @endif

            @if(session('success'))
                Swal.fire({
                    title: 'Sucesso!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    timer: 2000
                })
            @endif
        </script>
    </body>
</html>