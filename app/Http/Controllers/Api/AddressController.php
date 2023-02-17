<?php

namespace App\Http\Controllers\Api;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\AddressResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\AddressApiCreateRequest;

class AddressController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $Address = Address::where('user_id',$request->user_id)->get();
        return $this->sendResponse($Address,'');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
       
     
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddressApiCreateRequest $request)
    {
        try
        {
          $Address = Address::create($request->all());
           return $this->sendResponse($Address,'Address insert');
        }
        catch (\Exception $ex) 
        {
            return $this->sendResponse($ex->getMessage(),'');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  Attribute  $attribute
     * @return \Illuminate\Contracts\View\View
     */
    public function show(Address $Address)
    {
        $Address->get();
        return $this->sendResponse($Address,'');
       
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Address $Address)
    {
      
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param   Address $Address
     * @return \Illuminate\Http\Response
     */
    public function update(AddressApiUpdateRequest $request,Address $Address)
    {
        if($Address->update($request->all()))
        return new AddressResource($Address);
            
        return redirect()->back()->withErrors('Could not save Address');
       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Address $Address)
    {
        $Address->delete();
        return $this->sendResponse($Address,'Address Deleted');
    }
}
