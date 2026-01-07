<?php

namespace App\Http;

use Exception;
use Illuminate\Support\Facades\Http;

class AsaasHttpClient
{
    private string $token;

    private string $baseUrl;

    public function __construct()
    {
        $this->token = config('app.asaas_api_key');
        $this->baseUrl = config('app.asaas_url');
    }

    private function request(string $method, string $uri, array $data = [])
    {
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'content-type' => 'application/json',
            'access_token' => $this->token,
        ])->{$method}($this->baseUrl.$uri, $data);

        $json = $response->json();
        if ($response->failed() || isset($json['errors'])) {
            $message = $json['errors'][0]['description'] ?? 'Erro desconhecido comunicando com o Asaas.';
            throw new Exception($message);
        }

        return $json;
    }

    public function post(string $uri, array $data = [])
    {
        return $this->request('post', $uri, $data);
    }

    public function get(string $uri)
    {
        return $this->request('get', $uri);
    }

    public function put(string $uri)
    {
        return $this->request('put', $uri);
    }

    public function delete(string $uri)
    {
        return $this->request('delete', $uri);
    }
}
