<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CRMSService;
use GuzzleHttp\Exception\ClientException;
use Inertia\Inertia;
use Redirect;
use Auth;

class OpportunityController extends Controller
{

    private $crmss;

    public function __construct(CRMSService $crmss)
    {
        $this->crmss = $crmss;
    }



    public function index(Request $request)
    {   
        $user = Auth::user();
        $currentRMSStatus = $user->getCRMSStatus();
        if ($currentRMSStatus != 3)
            return Redirect::route('dashboard');

        $query = $request->all();
        $credentials = [
            'subdomain' => $user->sub_domain,
            'key' => $user->api_token
        ];
        $opportunities = [];
        $pagination = null;
        $errorMessage = null;
        try {
            $response = $this->crmss->opportunities($credentials, $query);
            $opportunities = $response['items'];
            $pagination = $response['meta'];
        } catch (ClientException $e) {
            $errorBody = json_decode($e->getResponse()->getBody(true), true);
            $errorMessage = $errorBody['errors'][0];
        }

        $rendered = Inertia::render('Opportunities', [
            'opportunities' => $opportunities,
            'pagination' => $pagination,
            'rmsStatus' => $currentRMSStatus
        ]);

        if ($errorMessage)
            $rendered->with('flash', ['error' => $errorMessage]);

        return $rendered;
    }

    public function Rental_opportunitie(Request $request)
    {
         echo "<pre>";print_r($request->all());exit();
    }
}
