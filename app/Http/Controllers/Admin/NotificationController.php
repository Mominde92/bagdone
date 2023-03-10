<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Order;
use App\Services\FCMService;
use App\Models\Item;
use Fcm;
use Benwilkins\FCM\FcmMessage;
use Illuminate\Bus\Queueable;
use App\Notifications\itemNotification;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        return view('admin.notification.all_notification');

    }

    public function itemnotification(Request $request)
    {
        $Items = Item::get();
        return view('admin.notification.item',compact('Items'));

    }

    public function senditemotification(Request $request)
    {
        $orders = Order::select('device_token')->distinct()->get();
        $item = item::where('id',$request->item_id)->first();

        $itemarray = $item->toArray();

        foreach($orders as $order)
        {
            $this->sendItem($order->device_token,$itemarray);
        }

        return redirect()->route('itemnotification')->with('success','Notification Send successful');
    }

    public static function sendItem($to,$item)
    {
            $item['main_screen_image'] = asset('uploads/items/' . $item['main_screen_image']);
            $item['cover_image'] = asset('uploads/items/' . $item['cover_image']);

            if($to == null ||  $to == '' )
            {
                return;
            }

        FCMService::send(
            $to,
          [
            'title_en'=>'title',
            "item"=>$item,
            "notification_type"=>'item',
          ]
      );
    }



    public function specificnotification(Request $request)
    {
        $orders = Order::get();
        return view('admin.notification.specific_notification',compact('orders'));

    }

    public function sendspecificnotification(Request $request)
    {
        $orders = Order::select('device_token')->distinct()->get();
        $item = item::where('id',$request->item)->first();

        foreach($orders as $order)
        {
            $this->sendNotificationToAll($order->device_token,'',$item,'','','','',$request->notification_type);
        }

        return redirect()->route('itemnotification')->with('success','Notification Send successful');
    }


    public function sendNotification(Request $request)
    {
        $orders = Order::select('device_token')->distinct()->get();
        $input['image_path'] = '';

        if ($image_path = $request->file('image_path')) {
            $destinationPath = 'uploads/notification/';
            $recordImage =  time(). "." . $image_path->getClientOriginalExtension();
            $image_path->move($destinationPath, $recordImage);
            $input['image_path'] = "$recordImage";
        }

        foreach($orders as $order)
        {
            $this->sendNotificationToAll($order->device_token,$request->title,$request->body,$request->title_locale,$request->description_locale,'',asset('/uploads/notification/'.$input['image_path']),'normal');
        }

        return redirect()->route('sendnotificationview')->with('success','Notification Send successful');

    }

    public static function sendNotificationToAll($to,$title=null,$body=null,$title_locale = null,$description_locale = null,$order_id = null ,$image_path ='https://www.bagdones.com/app/media/logos/bagdones_logo.png',$notification_type)
    {

        if(empty($image_path))
        {
        $image_path ='https://www.bagdones.com/app/media/logos/bagdones_logo.png';
        }

        if($to==null ||  $to == '' ){
            return 'not send';
        }


    FCMService::send(
        $to,
        [
            'title_en' => $title,
            'description_en' => $body,
            'title_locale' => $title_locale,
            'description_locale' => $description_locale,
            'order_id'=>$order_id,
            "notification_type"=>$notification_type,
            "image_path"=>$image_path,
        ]

    );
    return 'send';
    }

    public function firebase(Request $request)
    {
      return $this->sendNotificationToAll($request->device_token,$request->title_en,$request->description_en,$request->title_locale,$request->description_locale,'',asset('/uploads/notification/'.$request->image_path),'normal');
    }



}
