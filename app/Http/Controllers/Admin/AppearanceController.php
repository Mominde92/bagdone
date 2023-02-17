<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appearance;
use App\Models\Content_type;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;
use App\Http\Requests\AppearanceCreateRequest;
use App\Http\Requests\AppearanceUpdateRequest;


class AppearanceController extends Controller
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
            $data = Appearance::join('content_types', 'appearances.content_type_id', '=', 'content_types.id')
              		->get(['appearances.id', 'appearances.number','content_types.name as content_type']);

            return DataTables::of($data)->addIndexColumn()
                ->addColumn('action', 'admin.appearances.action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.appearances.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $content_types = Content_type::all();

        return view('admin.appearances.create', compact( 'content_types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AppearanceCreateRequest $request)
    {
        
        $input = $request->all();
        Appearance::create($input);

        return redirect()->action([AppearanceController::class, 'index'])->with('success','Appearance created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Appearance  $appearance
     * @return \Illuminate\Contracts\View\View
     */
    public function show(Appearance $appearance)
    {

        return view('admnin.appearances.show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Appearance  $appearance
     * @return \Illuminate\Contracts\View\View
     */
    public function edit( $id)
    {
        $appearance = Appearance::find($id);
        $content_types = Content_type::all();

        return view('admin.appearances.edit',compact('appearance','content_types'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Appearance  $appearance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AppearanceUpdateRequest $appearance)
    {
        $appearance->update($request->all());

        return redirect()->action([self::class, 'index'])->with('success','Appearance updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Appearance  $appearance
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $appearance = Appearance::where('id',$request->id)->delete();
        return Response()->json($appearance);
    }
}
