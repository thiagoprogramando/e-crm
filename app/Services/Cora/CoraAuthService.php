<?php

namespace App\Services\Cora;

use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;

class CoraAuthService {

    private $clientId;
    private $certPath;
    private $keyPath;
    private $url;
    
    
    public function __construct() {
        $this->clientId = env('API_BANK_TOKEN');
        $this->certPath = config('services.cora.certificate');
        $this->keyPath  = config('services.cora.key');
        $this->url      = env('API_BANK_URL');
    }

    public function getToken() {
        
        $token = $this->generateToken();
        return $token ?? null;
    }

    private function generateToken() {

        $client = new Client([
            'cert'     => $this->certPath,
            'ssl_key'  => $this->keyPath,
            'headers'  => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'verify' => false
        ]);

        $response = $client->post($this->url . 'token', [
            'form_params'    => [
                'grant_type' => 'client_credentials',
                'client_id'  => $this->clientId,
            ],
        ]);

        $json = json_decode($response->getBody()->getContents(), true);
        if (!isset($json['access_token'])) {
            return null;
        }

        return $json['access_token'];
    }
}
