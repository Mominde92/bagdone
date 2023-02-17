<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Item;
use DB;

class DashboardController extends Controller
{
    public function index()
    {
        $Orders = Order::count();
        $User = User::count();
        $is_online = User::where('is_online')->count();
        $Items = Item::count();
        $topitems = DB::select('select item_id, count(item_id),fs_items.name
        from fs_order_items
        JOIN fs_items ON fs_items.id=fs_order_items.item_id
        group by item_id ORDER BY count(item_id) DESC LIMIT 5');
        $list_orders = Order::select('id','amount','recived','in_process','in_delivery','deliverd')
            ->orderby('created_at','DESC')->get()->take(5);

        return view('admin.pages.dashboard', compact('Orders','User','is_online','Items','topitems','list_orders'));
    }
}
