<?php

namespace App\Clients;

use Exception;
use GuzzleHttp\Client;

class CRMSClient
{
    private $guzzle;
    private $credentials = [];
    private $headers = [];

    public function __construct()
    {
        $base = config('services.cmrs.uri');
        $this->guzzle = new Client([
            'cookies' => false,
            'base_uri' => "$base/",
            'http_errors' => true
            // 'debug' => true
        ]);
    }

    public function setCredentials($credentials = [])
    {
        if (!array_key_exists('subdomain', $credentials) || !array_key_exists('key', $credentials))
            throw new Exception('Incorrent or null credentials');
        $this->credentials = $credentials;
        $this->headers = [
            'X-SUBDOMAIN' => $this->credentials['subdomain'],
            'X-AUTH-TOKEN' => $this->credentials['key'],
            'Content-Type' => 'application/json'
        ];
    }

    public function getCredentials($credentials = [])
    {
        return $this->credentials;
    }

    public function get($endpoint, $query = [])
    {
        if (isset($query['all'])) {
            unset($query['all']);
            $query['page'] = 1;
            $query['per_page'] = 100;
            $items = [];
            do {
                $result = $this->request('get', $endpoint, $query);
                $items = array_merge($items, $result[$endpoint]);
                $query['page']++;
            } while ($result['meta']['row_count'] > 0);
            return $items;
        }
        $response = $this->request('get', $endpoint, $query);
           // echo "<pre>";print_r($response);exit();
        $response['items'] = $response[$endpoint];
        unset($response[$endpoint]);
        return $response;
    }

    public function put($endpoint, $body = [])
    {
        return $this->request('put', $endpoint, $body);
    }

    public function post($endpoint, $body = [])
    {
        return $this->request('post', $endpoint, $body);
    }

    private function request($method, $endpoint, $data)
    {
        $options = [
            'headers' => $this->headers
        ];

        $datakey = 'json';
        if (strtolower($method) === 'get')
            $datakey = 'query';
        $options[$datakey] = $data;

        $response = $this->guzzle->request($method, $endpoint, $options);
        return json_decode($response->getBody(), true);
    }
}
