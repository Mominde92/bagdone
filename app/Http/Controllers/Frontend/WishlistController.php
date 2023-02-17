<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;

class WishlistController extends Controller
{

    public function wishlist()
    {
        $Categories = Category::whereNull('parent_id')->get();
        return view('frontend.wishlist', compact('Categories'));
    }

    public function addToWishlist($id)
    {
        $product = Item::findOrFail($id);

        $wishlist = session()->get('wishlist', []);

        if(isset($wishlist[$id])) {
            $wishlist[$id]['quantity']++;
        } else {
            if($product->price != Null)
            {
                $price = $product->price ;
            }
            else
            {
                $price = $product->new_price ;
            }

            $wishlist[$id] = [
                "id" => $product->id,
                "name" => $product->name,
                "quantity" => 1,
                "price" => $price,
                "image" => $product->main_screen_image
            ];
        }

        session()->put('wishlist', $wishlist);
        return redirect()->back()->with('success', 'Product added to wishlist successfully!');
    }


    public function remove(Request $request)
    {
        if($request->id) {
            $wishlist = session()->get('wishlist');
            if(isset($wishlist[$request->id])) {
                unset($wishlist[$request->id]);
                session()->put('wishlist', $wishlist);
            }
            session()->flash('success', 'Product removed successfully');
        }
    }
}
