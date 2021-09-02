<?php

namespace App\Http\Controllers;

use App\Clients\CRMSClient;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CurrentRmsInformatonController extends Controller
{
public function update(Request $request)
{
	$input = $request->all();

	Validator::make($input, [
	'api_token' => ['required'],
	'sub_domain' => ['required'],
	'site_url' => ['required'],
	'user_type' => ['required']
	])->validateWithBag('updateCurrentRmsInformation');

	// Check if the current rms details pre-exits or not
	if (!is_null(Auth::user()->sub_domain) && !is_null(Auth::user()->api_token)) {
		throw ValidationException::withMessages([
		'api_token' => 'Please contact administrator to change the api token.'
		])->errorBag('updateCurrentRmsInformation');
	}

	// Check if the Current RMS details are valid or not
	try {
		$client = new CRMSClient();
		$client->setCredentials([
		'key' => $input['api_token'],
		'subdomain' => $input['sub_domain'],
		'site_url' => $input['site_url'],
		'user_type' => $input['user_type']
		]);
		$client->get('members', ['page' => '1', 'per_page' => '1']);
	} catch (Exception $ex) {
		throw ValidationException::withMessages([
		'api_token' => 'The provided api token is not valid.'
		])->errorBag('updateCurrentRmsInformation');
	}

	$user = Auth::user();
	$user->api_token = $request->input('api_token');
	$user->sub_domain = $request->input('sub_domain');
	$user->site_url = $request->input('site_url');
	$user->user_type = $request->input('user_type');
	$user->save();

	return back(303)->with('status', 'Settings-updated');
	}
}