<?php

namespace App\Services;

use App\Clients\CRMSClient;

class CRMSService
{
    private $client;

    public function __construct()
    {
        $this->client = new CRMSClient();
    }

    public function check($credentials)
    {
        // TODO: RUN REQUEST TO REMOTE VALIDATION
    }

    public function products($credentials, $query = [])
    {
        $this->client->setCredentials($credentials);
        return $this->client->get('products', $query);
    }

    public function productGroups($credentials, $query = [])
    {
        $this->client->setCredentials($credentials);
        return $this->client->get('product_groups', $query);
    }

    public function members($credentials, $query = [])
    {
        $this->client->setCredentials($credentials);
        return $this->client->get('members', $query);
    }

    public function opportunities($credentials, $query = [])
    {
        $this->client->setCredentials($credentials);
        return $this->client->get('opportunities', $query);
    }
}
