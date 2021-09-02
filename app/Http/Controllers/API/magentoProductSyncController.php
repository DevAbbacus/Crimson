<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rental_opportunitie;

class magentoProductSyncController extends Controller
{


    public function magentoProducts(Request $request)
    {
        $request_data = $request->all();
        
        $Rental_opportunitie_exist = Rental_opportunitie::where('answer_id', '=', $request_data['answer_id'])->first();
          if ($Rental_opportunitie_exist === null) {
            $request_data_insert = array(
                                'answer_id' => $request_data['answer_id'],
                                'form_id' => $request_data['form_id'],
                                'store_id' => $request_data['store_id'],
                                'created_at' => $request_data['created_at'],
                                'ip' => $request_data['ip'],
                                'customer_id' => $request_data['customer_id'],
                                'response_json' => $request_data['response_json'],
                                'admin_response_email' => $request_data['admin_response_email'],
                                'admin_response_message' => $request_data['admin_response_message'],
                                'admin_response_status' => $request_data['admin_response_status'],
                                'referer_url' => $request_data['referer_url'],
                                'bookingdata' => $request_data['bookingdata'],
                                 );  

                $request_data = Rental_opportunitie::create($request_data_insert);
               $resource = array('massage' => 'Opportunitie Data inserted successfully!',
                                    'status' => 1,
                                    'answer_id' => $request_data['answer_id']);
               return json_encode($resource);

            }else{
                $resource = array('massage' => 'Opportunitie Data already exist!',
                                    'status' => 0,
                                    'answer_id' => $request_data['answer_id'] );
               return json_encode($resource);
            
            }
       
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
