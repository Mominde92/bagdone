<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Http\Requests\SubCategoryCreateRequest;
use App\Http\Requests\SubCategoryUpdateRequest;
use App\Helpers\ImageHelper;
use App\Models\Item;
use App\Models\ItemImage;


class SubCategoryController extends Controller
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

            $data = Category::select('id','name','name_locale','image', 'parent_id')
            ->where('parent_id','!=', 0)->get();

            $results = [];
            foreach($data as $category)
            {
                $category['parent_name']= $category->get_parent->name;
                $category->image = asset('uploads/category/' . $category->image);
                array_push($results, $category);
            }

            return DataTables::of($results)->addIndexColumn()
                ->addColumn('action', 'admin.subCategories.action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.subCategories.index');


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('admin.subCategories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SubCategoryCreateRequest $request)
    {

       $input = $request->all();

       $input['image'] = ImageHelper::handleUploadedImage($request->file('image'),'uploads/category/');
       $input['cover_image'] = ImageHelper::handleUploadedImage($request->file('cover_image'),'uploads/category/');
       $input['slider_web'] = ImageHelper::handleUploadedImage($request->file('slider_web'),'uploads/category/');

        Category::create($input);

        return redirect()->route('subCategory.index')->with('success','Sub Category created successfully.');
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

        return view(admin.subCategories.show,compact(category));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        $category = category::find($category->id);

        return view('admin.subCategories.edit',compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(SubCategoryUpdateRequest $request, Category $category)
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

        return redirect()->route('subCategory.index')->with('success','Sub Category updated successfully');
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
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
        
        
        ImageHelper::handleDeletedImage($Category->image,'uploads/category/');
        ImageHelper::handleDeletedImage($Category->cover_image,'uploads/category/');
        ImageHelper::handleDeletedImage($Category->slider_web,'uploads/category/');

        $Category = $Category->delete();
        return Response()->json($Category);
    }

    public function dataAjax(Request $request)
    {
        $search = $request->search;

        if($search == '')
        {
           $categories = Category::orderby('name','asc')->select('id','name')->limit(5)->get();
        }
        else
        {
           $categories = Category::orderby('name','asc')->select('id','name')->where('name', 'like', '%' .$search . '%')->limit(5)->get();
        }

        foreach($categories as $category)
        {
           $response[] = array(
                "id"=>$category->id,
                "text"=>$category->name
           );
        }

        return response()->json($response);
    }

}
