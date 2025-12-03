<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Search;
use App\Models\SearchExtract;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class SearchController extends Controller {
    
    public function index (Request $request) {

        $searchs = Search::get();
        return view('app.Search.index', [
            'searchs' => $searchs
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
            return redirect()->back()->with('success', 'Busca gerada com sucesso!');
        } 

        return redirect()->back()->with('error', 'Ocorreu um erro ao gerar a busca, tente novamente mais tarde!');
    }

    public function search (Request $request, $uuid) {

        $search = Search::where('uuid', $uuid)->first();
        if (!$search || $search->status != 'active') {
            return redirect()->back()->with('error', 'Consulta indisponível!');
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
            $extract->search_id    = $search->id;
            $extract->request      = json_encode($options['json']);
            $extract->response     = json_encode($data);
            $extract->is_paid      = true;
            $extract->cpfcnpj            = preg_replace('/[^0-9]/', '', $request->cpfcnpj);
            $extract->status_code        = data_get($data, 'HEADER.INFORMACOES_RETORNO.STATUS_RETORNO.CODIGO');
            $extract->status_description = data_get($data, 'HEADER.INFORMACOES_RETORNO.STATUS_RETORNO.DESCRICAO');
            if ($extract->save()) {
                return redirect()->back()->with('success', 'Busca realizada com sucesso!');
            }

        } catch (RequestException $e) {
            return redirect()->back()->with('infor', 'Não foi possível realizar a Consulta, tente novamente mais tarde!');
        } catch (\Exception $e) {
            return redirect()->back()->with('infor', 'Não foi possível realizar a Consulta, tente novamente mais tarde!');
        }

        return redirect()->back()->with('infor', 'Não foi possível realizar a Consulta, tente novamente mais tarde!');
    }

    private function formatValue($valor) {
        
        $valor = preg_replace('/[^0-9,]/', '', $valor);
        $valor = str_replace(',', '.', $valor);
        $valorFloat = floatval($valor);
    
        return number_format($valorFloat, 2, '.', '');
    }
}
