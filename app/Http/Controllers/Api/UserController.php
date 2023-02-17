<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserResource;
use App\Http\Requests\UserApiCreateRequest;
use App\Http\Requests\UserApiUpdateRequest;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $User = User::get();
        return $this->sendResponse($User,'');

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserApiCreateRequest $request)
    {

        try 
        {
           if ($image = $request->file('image_path'))
            {
            $destinationPath = 'uploads/users/';
            $recordImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $recordImage);
            $input['image_path'] = "$recordImage";
            }
            $input['token'] =  bcrypt($request->user_token);

            $User = User::create($request->all());

          return $this->sendResponse($input['token'],'User insert');

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
    public function show(User $User)
    {
        $User->get();
        return $this->sendResponse($User,'');

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $User)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param   Address $Address
     * @return \Illuminate\Http\Response
     */
    public function update(UserApiUpdateRequest $request,User $User)
    {
        if($User->update($request->all()));
        return new UserResource($User);

        return redirect()->back()->withErrors('Could not save User');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $User)
    {
        $User->delete();
        return $this->sendResponse($User,'User Deleted');
    }
}
