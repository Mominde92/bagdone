<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\Language;
use App\Http\Requests\UsersCreateRequest;
use App\Http\Requests\UsersUpdateRequest;

class UsersController extends Controller
{
    protected $namespace = null;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) 
        {
            $data = User::get();

            $results = [];
            foreach($data as $User)
            {
                $User->image = asset('uploads/User/' . $User->image);
                array_push($results, $User);
            }

            return DataTables::of($results)->addIndexColumn()
                ->addColumn('action', 'admin.users.action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.users.index');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $languages = Language::get();

        return view('users.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UsersCreateRequest $request)
    {
        $input = $request->all();

    if ($image = $request->file('image')) 
    {
        $destinationPath = 'uploads/User/';
        $recordImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
        $image->move($destinationPath, $recordImage);
        $input['image'] = "$recordImage";
    }

        User::create($input);
        return redirect()->action([UserController::class, 'index'])->with('success','User created successfully.');;

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $User
     * @return \Illuminate\Http\Response
     */
    public function show(User $User)
    {
        $User = User::find($User->id);


        return view('admin.users.show',compact('User'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $User
     * @return \Illuminate\Http\Response
     */
    public function edit(User $User)
    {
        $User = User::find($User->id);

        return view('admin.users.edit',compact('User'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $User
     * @return \Illuminate\Http\Response
     */
    public function update(UsersUpdateRequest $request, User $User)
    {

        $input = $request->all();

        if ($image = $request->file('image')) 
        {
            $destinationPath = 'uploads/User/';
            $recordImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $recordImage);
            $input['image'] = "$recordImage";
        }
        else
        {
            unset($input['image']);
        }

        $User->update($input);

        return redirect()->action([UserController::class, 'index'])->with('success','User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $User
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $Users = User::where('id',$request->id)->delete();
        return Response()->json($Users);
    }


}
