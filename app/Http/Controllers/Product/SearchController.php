<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Search;
use App\Models\SearchExtract;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class SearchController extends Controller {
    
    public function index (Request $request) {

        $searchs  = Search::get();
        $extracts = SearchExtract::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->paginate(15);
        return view('app.Search.index', [
            'searchs'  => $searchs,
            'extracts' => $extracts
        ]);
    }

    public function data ($uuid) {

        $data = SearchExtract::where('uuid', $uuid)->first();
        if (!$data) {
            return redirect()->back()->with('error', 'Registro não encontrado!');
        }

        $search = Search::where('id', $data->search_id)->first();
        if (!$search) {
            return redirect()->back()->with('error', 'Consulta não encontrada!');
        }

        $response = json_decode($data->response, true);

        $retornouReceita = data_get($response, 'HEADER.DADOS_RETORNADOS.DADOS_RECEITA_FEDERAL');
        if ($retornouReceita == "1") {

            $dadosReceita = data_get($response, 'CREDCADASTRAL.DADOS_RECEITA_FEDERAL', []);
            if (!empty($dadosReceita)) {
                $pessoa = [
                    "NOME"                => $dadosReceita['NOME'] ?? null,
                    "NUMERO_DOC"          => data_get($response, 'HEADER.PARAMETROS.CPFCNPJ'),
                    "TIPO_PESSOA"         => $dadosReceita['TIPO_PESSOA'] ?? null,
                    "SITUACAO_JURIDICA"   => $dadosReceita['SITUACAO_RECEITA'] ?? null,
                    "DATA_ABERTURA"       => $dadosReceita['DATA_NASCIMENTO_FUNDACAO'] ?? null,
                    "NATUREZA_JURIDICA"   => $dadosReceita['NATUREZA_JURIDICA'] ?? null,
                    "TELEFONE"            => $dadosReceita['TELEFONE'] ?? '-',
                    "LOGRADOURO"          => $dadosReceita['LOGRADOURO'] ?? '',
                    "NUMERO"              => $dadosReceita['NUMERO'] ?? '',
                    "BAIRRO"              => $dadosReceita['BAIRRO'] ?? '',
                    "CIDADE"              => $dadosReceita['CIDADE'] ?? '',
                    "UF"                  => $dadosReceita['UF'] ?? '',
                    "CEP"                 => $dadosReceita['CEP'] ?? '',
                ];
            }
        }

        $retornouEmpresa = data_get($response, 'HEADER.DADOS_RETORNADOS.INFORMACOES_DA_EMPRESA');
        if ($retornouEmpresa == "1") {

            $dadosEmpresa = data_get($response, 'CREDCADASTRAL.INFORMACOES_DA_EMPRESA', []);
            if (!empty($dadosEmpresa)) {
                $pessoa = [
                    "NOME"                => $dadosEmpresa['RAZAO_SOCIAL'] ?? null,
                    "NOME_FANTASIA"       => $dadosEmpresa['NOME_FANTASIA'] ?? null,
                    "NUMERO_DOC"          => data_get($response, 'HEADER.PARAMETROS.CPFCNPJ'),
                    "SITUACAO"            => $dadosEmpresa['SITUACAO'] ?? null,
                    "DATA_ABERTURA"       => $dadosEmpresa['DATA_FUNDACAO'] ?? null,
                    "DATA_ENCERRAMENTO"   => $dadosEmpresa['DATA_ENCERRAMENTO'] ?? null,
                    "NATUREZA_JURIDICA"   => $dadosEmpresa['NATUREZA_JURIDICA'] ?? null,
                ];
            }
        }

        $retornouPendFin = data_get($response, 'HEADER.DADOS_RETORNADOS.PENDENCIAS_FINANCEIRAS');
        if ($retornouPendFin == "1") {

            $dadosPend = data_get($response, 'CREDCADASTRAL.PEND_FINANCEIRAS', []);
            if (!empty($dadosPend)) {

                $pendencias = [
                    "quantidade"  => $dadosPend['QUANTIDADE_OCORRENCIA'] ?? 0,
                    "provedores"  => $dadosPend['PROVEDORES'] ?? [],
                    "ocorrencias" => []
                ];

                foreach ($dadosPend['OCORRENCIAS'] ?? [] as $item) {
                    $pendencias["ocorrencias"][] = [
                        "data_vencimento" => $item['DATA_VENCIMENTO'] ?? '',
                        "valor"           => $item['VALOR'] ?? '',
                        "credor"          => $item['CREDOR'] ?? '',
                        "contrato"        => $item['CONTRATO'] ?? '',
                        "modalidade"      => $item['MODALIDADE'] ?? '',
                        "informante"      => $item['INFORMANTE'] ?? '',
                    ];
                }
            }
        }

        $retornouPassagens = data_get($response, 'HEADER.DADOS_RETORNADOS.PASSAGENS_COMERCIAIS');
        if ($retornouPassagens == "1") {

            $dadosPass = data_get($response, 'CREDCADASTRAL.PASSAGENS_COMERCIAIS', []);
            if (!empty($dadosPass)) {

                $passagens = [
                    "quantidade"  => $dadosPass['QUANTIDADE_OCORRENCIA'] ?? 0,
                    "ocorrencias" => []
                ];

                foreach ($dadosPass['OCORRENCIAS'] ?? [] as $item) {
                    $passagens["ocorrencias"][] = [
                        "data_consulta" => $item['DATA_CONSULTA'] ?? '',
                        "cliente"       => $item['CLIENTE_CONSULTA'] ?? '',
                        "telefone"      => $item['TELEFONE_CLIENTE'] ?? '',
                        "cidade_uf"     => $item['CIDADE_UF_CLIENTE'] ?? '',
                    ];
                }
            }
        }

        $retornouProtesto = data_get($response, 'HEADER.DADOS_RETORNADOS.PROTESTO_ANALITICO');
        if ($retornouProtesto == "1") {

            $dadosProtesto = data_get($response, 'CREDCADASTRAL.PROTESTOS', []);

            if (!empty($dadosProtesto)) {

                $protestoAnalitico = [
                    "quantidade"  => $dadosProtesto['QUANTIDADE_OCORRENCIA'] ?? 0,
                    "valor_total" => $dadosProtesto['VALOR_TOTAL'] ?? '',
                    "ultimo"      => $dadosProtesto['ULTIMO_PROTESTO'] ?? '',
                    "ocorrencias" => []
                ];

                foreach ($dadosProtesto['OCORRENCIAS'] ?? [] as $item) {
                    $protestoAnalitico["ocorrencias"][] = [
                        "data"        => $item['DATA'] ?? '',
                        "moeda"       => $item['MOEDA'] ?? '',
                        "valor"       => $item['VALOR'] ?? '',
                        "origem"      => $item['ORIGEM'] ?? '',
                        "credor"      => $item['CREDOR'] ?? '',
                        "tipo"        => $item['TIPO_ANOTACAO'] ?? '',
                    ];
                }
            }
        }

        $retornouCheques = data_get($response, 'HEADER.DADOS_RETORNADOS.CCF_BACEN');
        if ($retornouCheques == "1") {

            $dadosCheques = data_get($response, 'CREDCADASTRAL.CH_SEM_FUNDOS_BACEN', []);
            if (!empty($dadosCheques)) {

                $chequesSemFundo = [
                    "quantidade"   => $dadosCheques['QUANTIDADE_OCORRENCIA'] ?? 0,
                    "correntista"  => $dadosCheques['CORRENTISTA'] ?? '',
                    "tipo_pessoa"  => $dadosCheques['TIPO_PESSOA'] ?? '',
                    "documento"    => $dadosCheques['CPFCNPJ'] ?? '',
                    "ocorrencias"  => []
                ];

                foreach ($dadosCheques['OCORRENCIAS'] ?? [] as $item) {
                    $chequesSemFundo["ocorrencias"][] = [
                        "banco"        => $item['NUM_BANCO'] ?? '',
                        "agencia"      => $item['NUM_AGENCIA'] ?? '',
                        "motivo"       => $item['MOTIVO_DEVOLUCAO'] ?? '',
                        "qtd_cheques"  => $item['QTD_CHEQUES'] ?? '',
                        "data"         => $item['DT_ULTIMA_OCORRENCIA'] ?? '',
                    ];
                }
            }
        }

        $retornouProtestoSintetico = data_get($response, 'HEADER.DADOS_RETORNADOS.PROTESTO_SINTETICO');
        if ($retornouProtestoSintetico == "1") {

            $dadosProtestoSintetico = data_get($response, 'CREDCADASTRAL.PROTESTO_SINTETICO', []);

            if (!empty($dadosProtestoSintetico)) {

                $protestoSintetico = [
                    "quantidade"  => $dadosProtestoSintetico['QUANTIDADE_OCORRENCIA'] ?? 0,
                    "valor_total" => $dadosProtestoSintetico['VALOR_TOTAL'] ?? '',
                    "ultimo"      => $dadosProtestoSintetico['ULTIMO_PROTESTO'] ?? '',
                    "ocorrencias" => []
                ];

                foreach ($dadosProtestoSintetico['OCORRENCIAS'] ?? [] as $item) {
                    $protestoSintetico["ocorrencias"][] = [
                        "uf"         => $item['UF'] ?? '',
                        "telefone"   => $item['TELEFONE'] ?? '',
                        "endereco"   => $item['ENDERECO'] ?? '',
                        "cartorio"   => $item['CARTORIO'] ?? '',
                        "comarca"    => $item['COMARCA'] ?? '',
                        "info"       => $item['INFO_CARTORIOS'] ?? '',
                        "data"       => $item['DATA_OCORRENCIA'] ?? '',
                        "atualizado" => $item['DATA_ATUALIZACAO'] ?? '',
                        "valor"      => $item['VALOR'] ?? '',
                        "credor"     => $item['CREDOR'] ?? '',
                        "cedente"    => $item['CEDENTE'] ?? '',
                    ];
                }
            }
        }

        $retornouContumacia = data_get($response, 'HEADER.DADOS_RETORNADOS.CONTUMACIA');
        if ($retornouContumacia == "1") {

            $dadosContumacia = data_get($response, 'CREDCADASTRAL.CONTUMACIA', []);

            if (!empty($dadosContumacia)) {

                $contumacia = [
                    "quantidade"      => $dadosContumacia['QUANTIDADE_OCORRENCIA'] ?? 0,
                    "data_primeira"   => $dadosContumacia['DATA_PRIMEIRA_OCORRENCIA'] ?? '',
                    "data_ultima"     => $dadosContumacia['DATA_ULTIMA_OCORRENCIA'] ?? '',
                ];
            }
        }

        $retornouScore = data_get($response, 'HEADER.DADOS_RETORNADOS.SCORE');
        if ($retornouScore == "1") {

            $dadosScore = data_get($response, 'CREDCADASTRAL.SCORES', []);
            if (!empty($dadosScore)) {

                $score = [
                    "quantidade"  => $dadosScore['QUANTIDADE_OCORRENCIAS'] ?? 0,
                    "ocorrencias" => []
                ];

                foreach ($dadosScore['OCORRENCIAS'] ?? [] as $item) {
                    $score["ocorrencias"][] = [
                        "tipo"              => $item['TIPO_SCORE'] ?? '',
                        "valor"             => $item['SCORE'] ?? '',
                        "classificacao"     => $item['CLASSIF_NRO'] ?? '',
                        "classificacao_abc" => $item['CLASSIF_ABC'] ?? '',
                        "probabilidade"     => $item['PROBABILIDADE_INADIMPLENCIA'] ?? '',
                        "descricao"         => $item['DESCR_SCORE'] ?? '',
                        "risco_texto"       => $item['RISCO'] ?? '',
                    ];
                }
            }
        }

        return view('app.Search.view', [
            'data'              => $data,
            'search'            => $search,
            'pessoa'            => $pessoa ?? [],
            'pendencias'        => $pendencias ?? [],
            'passagens'         => $passagens ?? [],
            'protestoAnalitico' => $protestoAnalitico ?? [],
            'protestoSintetico' => $protestoSintetico ?? [],
            'chequesSemFundo'   => $chequesSemFundo ?? [],
            'contumacia'        => $contumacia ?? [],
            'score'             => $score ?? []
        ]);
    }

    public function store (Request $request) {

        $search                 = new Search();
        $search->uuid           = Str::uuid();
        $search->title          = $request->title;
        $search->content        = $request->content;
        $search->value          = $this->formatValue($request->value);
        $search->addition       = $this->formatValue($request->addition);
        $search->api_url        = $request->api_url;
        $search->api_method     = $request->api_method;
        $search->api_token      = $request->api_token;
        $search->api_code       = $request->api_code;
        $search->api_version    = $request->api_version;
        if ($search->save()) {
            return redirect()->back()->with('success', 'Consulta gerada com sucesso!');
        } 

        return redirect()->back()->with('error', 'Ocorreu um erro ao gerar a Consulta, tente novamente mais tarde!');
    }

    public function update (Request $request, $uuid) {

        $search = Search::where('uuid', $uuid)->first();
        if (!$search) {
            return redirect()->back()->with('error', 'Busca não encontrada!');
        }

        if ($request->has('title')) {
            $search->title = $request->title;
        }
        if ($request->has('content')) {
            $search->content = $request->content;
        }
        if ($request->has('value')) {
            $search->value = $this->formatValue($request->value);
        }
        if ($request->has('addition')) {
            $search->addition = $this->formatValue($request->addition);
        }
        if ($request->has('api_url')) {
            $search->api_url = $request->api_url;
        }
        if ($request->has('api_method')) {
            $search->api_method = $request->api_method;
        }
        if ($request->has('api_token')) {
            $search->api_token = $request->api_token;
        }
        if ($request->has('api_code')) {
            $search->api_code = $request->api_code;
        }
        if ($request->has('api_version')) {
            $search->api_version = $request->api_version;
        }
        if ($search->save()) {
            return redirect()->back()->with('success', 'Consulta atualizada com sucesso!');
        } 

        return redirect()->back()->with('error', 'Ocorreu um erro ao atualizar a Consulta, tente novamente mais tarde!');
    }

    public function search (Request $request, $uuid) {

        $search = Search::where('uuid', $uuid)->first();
        if (!$search || $search->status != 'active') {
            return redirect()->back()->with('error', 'Consulta indisponível!');
        }

        if (Auth::user()->wallet_cash < ($search->value + $search->addition)) {
            return redirect()->back()->with('infor', 'Saldo insuficiente para realizar a Consulta, por favor realize um Depósito!');
        }

        try {

            $client  = new Client();
            $options = [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'accept'       => 'application/json',
                    'access_token' => env('API_BANK_TOKEN'),
                    'User-Agent'   => env('APP_NAME')
                ],
                'json' => [
                    'CodigoProduto' => $search->api_code,
                    'Versao'        => $search->api_version,
                    'ChaveAcesso'   => $search->api_token,
                    'Parametros'    => [
                        'CPFCNPJ'    => preg_replace('/[^0-9]/', '', $request->cpfcnpj),
                        'TipoPessoa' => (strlen(preg_replace('/[^0-9]/', '', $request->cpfcnpj)) == 11) ? 'F' : 'J'
                    ],
                ],
                'verify' => false
            ];

            $response = $client->post($search->api_url, $options);
            $data = json_decode($response->getBody()->getContents(), true);

            $extract = new SearchExtract();
            $extract->uuid         = Str::uuid();
            $extract->user_id      = auth()->user()->id;
            $extract->search_id    = $search->id;
            $extract->request      = json_encode($options['json']);
            $extract->response     = json_encode($data);
            $extract->is_paid      = true;
            $extract->cpfcnpj            = preg_replace('/[^0-9]/', '', $request->cpfcnpj);
            $extract->status_code        = data_get($data, 'HEADER.INFORMACOES_RETORNO.STATUS_RETORNO.CODIGO');
            $extract->status_description = data_get($data, 'HEADER.INFORMACOES_RETORNO.STATUS_RETORNO.DESCRICAO');
            if ($extract->save()) {
                Auth::user()->decrement('wallet_cash', $search->value + $search->addition);
                return redirect()->back()->with('success', 'Busca realizada com sucesso!');
            }
        } catch (RequestException $e) {
            return redirect()->back()->with('infor', 'Não foi possível realizar a Consulta, tente novamente mais tarde!');
        } catch (\Exception $e) {
            return redirect()->back()->with('infor', 'Não foi possível realizar a Consulta, tente novamente mais tarde!');
        }
    }

    private function formatValue($valor) {
        
        $valor = preg_replace('/[^0-9,]/', '', $valor);
        $valor = str_replace(',', '.', $valor);
        $valorFloat = floatval($valor);
    
        return number_format($valorFloat, 2, '.', '');
    }
}
