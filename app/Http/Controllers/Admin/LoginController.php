<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Redirect;
use Mail;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Models\Category;

class LoginController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $Categories = Category::whereNull('parent_id')->get();
        $subCategories = Category::wherenotNull('parent_id')->wherein('id',['40','27','49','47'])->get()->take(4);

      return view('frontend.register',compact('Categories','subCategories'));
    }

    public function register_user(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'email' => 'required|min:4|unique:users',
            'password' => 'required|min:6',
        ]);

        if ($validator->passes())
        {
            $user = new user();
            $user->full_name = $request->fname . ' ' . $request->lname ;
            $user->email = $request->email ;
            $user->password = bcrypt($request->password) ;
            $user->save();

            return redirect()->route('ecommerce')->with('succes', 'Successfully Registered');
        }
        return redirect::back()->with('succes', '');
    }

    public function login(Request $request)
    {
        $Categories = Category::whereNull('parent_id')->get();
        $subCategories = Category::wherenotNull('parent_id')->wherein('id',['40','27','49','47'])->get()->take(3);

      return view('frontend.login',compact('Categories','subCategories'));
    }

    public function login_user(Request $request)
    {
     $email = $request->email;
     $password = $request->password;

    }


    public function forget_pwd(Request $request)
    {
        $Categories = Category::whereNull('parent_id')->get();
        $subCategories = Category::wherenotNull('parent_id')->wherein('id',['40','27','49','47'])->get()->take(4);

        return view('frontend.forget_pwd',compact('Categories','subCategories'));
    }

    public function forgetPwdUser(Request $request)
    {
    $user = User::where('email',$request->email)->first();
    $user->otp = md5($request->email.now()) ;
    $user->update();

    $data = ['user'=> $user];
    $email =  $request->email;

    try
    {
        Mail::send(['html'=>'forget_password'], $data, function($message) use ($email) {
            $message->to($email, 'Forget Password')->subject('Bagdones');
         });
    }
    catch (\Exception $e)
    {
        return redirect()->route('ecommerce')->with('succes', $e->getMessage());
    }


    $order_id = '55';
    $data = ['order_id'=> $order_id];


    // Mail::send(['html'=>'mail.order.recived'], $data, function($message) use ($email) {
    //     $message->to($email, 'Order status')->subject('Bagdones');
    //     $message->from('xyz@gmail.com','Order status');
    //  });

    $emailContents['date'] = Carbon::now();

    // Helper::sendEmail($email,$emailContents, '[forget_password',  'Forget Password');


    return redirect()->route('ecommerce')->with('succes', 'The Email has been sent');
    }

    public function resetPassword(Request $request)
    {
        $otp = $request->otp ;
        $user = User::where('otp',$otp)->first();
        $Categories = Category::whereNull('parent_id')->get();
        $subCategories = Category::wherenotNull('parent_id')->wherein('id',['40','27','49','47'])->get()->take(4);

        if($user)
        {
            return view('frontend.resetpassword',compact('user','Categories','subCategories'));
        }
        else
        {
            return redirect()->route('ecommerce');
        }

    }

    public function resetPasswordPost(Request $request)
    {
       $otp =  $request->otp ;
       $password = $request->password ;
       $user = User::Where('otp',$otp)->first();
       $user->password = bcrypt($password);
       $user->update();

       return redirect()->route('ecommerce')->with('succes', 'Rest Password Successfull');
    }



  }


