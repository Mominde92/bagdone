<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Http\Requests\CategoryCreateRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Helpers\ImageHelper;
use App\Models\Item;

class CategoryController extends Controller
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
            $data = Category::select('id','name','name_locale','image', 'parent_id' )
            ->where('parent_id', null)->get();

            $results = [];
            foreach($data as $category)
            {
                $category->image = asset('uploads/category/' . $category->image);
                array_push($results, $category);
            }

            return DataTables::of($results)->addIndexColumn()
                ->addColumn('action', 'admin.categories.action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.categories.index');


    }

    public function dataAjax(Request $request)
    {
        $search = $request->search;

        if($search == '')
        {
           $categories = Category::orderby('name','asc')->select('id','name')->limit(15)->WhereNull('parent_id')->get();
        }
        else
        {
           $categories = Category::orderby('name','asc')->select('id','name')->where('name', 'like', '%' .$search . '%')->WhereNull('parent_id')->limit(15)->get();
        }

        foreach($categories as $category){
           $response[] = array(
                "id"=>$category->id,
                "text"=>$category->name
           );
        }
        return response()->json($response);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryCreateRequest $request)
    {
        $input = $request->all();

        $input['image'] = ImageHelper::handleUploadedImage($request->file('image'),'uploads/category/');
        $input['cover_image'] = ImageHelper::handleUploadedImage($request->file('cover_image'),'uploads/category/');
        $input['slider_web'] = ImageHelper::handleUploadedImage($request->file('slider_web'),'uploads/category/');

        Category::create($input);
        return redirect()->action([CategoryController::class, 'index'])->with('success','Category created successfully.');;

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        $category = Category::find($category->id);
    
        return view('admin.categories.show',compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        $category = Category::find($category->id);

        return view('admin.categories.edit',compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryUpdateRequest $request, Category $category)
    {

        $input = $request->all();

        if ($image = $request->file('image'))
        {
            $input['image'] = ImageHelper::handleUpdatedUploadedImage($image,'/uploads/category/',$category,'/uploads/category/','image');
        }

        if ($cover_image = $request->file('cover_image'))
        {
            $input['cover_image'] = ImageHelper::handleUpdatedUploadedImage($cover_image,'/uploads/category/',$category,'/uploads/category/','cover_image');
        }

        if ($slider_web = $request->file('slider_web'))
        {
            $input['slider_web'] = ImageHelper::handleUpdatedUploadedImage($slider_web,'/uploads/category/',$category,'/uploads/category/','slider_web');
        }

        $category->update($input);

        return redirect()->action([CategoryController::class, 'index'])->with('success','Category updated successfully');
  }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(request $request)
    {
        $Category = Category::find($request->id);
        
        if(!is_null($Category->items))
        {
            foreach($Category->items as $item)
            {
                $items = Item::where('id',$item->id)->get();
                foreach($items as $item)
                {
                 $images = ItemImage::where('item_id',$item->id)->get();
                 foreach($images as $image)
                {
                    ImageHelper::handleDeletedImage($image->image,'uploads/items/');
                    $image->delete();
                }
                }
                
                foreach($items as $item)
                {
                    ImageHelper::handleDeletedImage($item->main_screen_image,'uploads/items/');
                    ImageHelper::handleDeletedImage($item->cover_image,'uploads/items/');
                    ImageHelper::handleDeletedImage($item->slider_web,'uploads/items/');
                    
                    $item->delete();
                }
            }
        }
        

        foreach($Category->get_childern as $get_child)
        {
         $subCategory = Category::where('id',$get_child->id)->first();
         print_r($subCategory) and die();
        }


        ImageHelper::handleDeletedImage($Category->image,'uploads/category/');
        ImageHelper::handleDeletedImage($Category->cover_image,'uploads/category/');
        ImageHelper::handleDeletedImage($Category->slider_web,'uploads/category/');

        // $Category = $Category->delete();
        // return Response()->json($Category);
    }


}
