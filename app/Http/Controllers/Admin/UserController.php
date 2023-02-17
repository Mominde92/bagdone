<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Socialite;

class UserController extends Controller
{
    function index()
    {
        return view('admin.login');
    }


    function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if(!Auth::attempt($credentials))
        {
            return back()->with('succes_message', 'Email or Password not matched');
        }
        elseif(auth()->guard('web')->user()->role_id == '1')
        {
            return redirect('/');
        }
        elseif(auth()->guard('web')->user()->role_id == '2')
        {
            return redirect()->route('ecommerce');
        }

    }
    public function logout()
    {
        Auth::logout();
        return redirect()->route('ecommerce');
    }

    /** **/

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleGoogleCallback()
    {
        try
         {
            $user = Socialite::driver('google')->user();

            $finduser = User::where('google_id', $user->id)->first();

            if($finduser)
            {
                Auth::login($finduser);

                return redirect('/ecommerce');

            }
            else
            {
                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'google_id'=> $user->id,
                    'password' => encrypt('123456dummy')
                ]);

                Auth::login($newUser);

                return redirect('/ecommerce');
            }

        }
         catch (Exception $e) 
        {
            dd($e->getMessage());
        }
    }

                /**
             * Login Using Facebook
             */
            public function loginUsingFacebook()
            {
                return Socialite::driver('facebook')->redirect();
            }

            public function callbackFromFacebook()
            {
            try {
                $user = Socialite::driver('facebook')->user();

                $saveUser = User::updateOrCreate([
                    'facebook_id' => $user->getId(),
                ],[
                    'name' => $user->getName(),
                    'email' => $user->getEmail(),
                    'password' => Hash::make($user->getName().'@'.$user->getId())
                        ]);

                Auth::loginUsingId($saveUser->id);

                return redirect()->route('home');
                } 
                catch (\Throwable $th)
                {
                    throw $th;
                }
            }
}



