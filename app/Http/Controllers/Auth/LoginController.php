<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite; 
use App\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;
     
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function redirectToProvider()
    {
        return Socialite::driver('facebook')->redirect();
    }
    public function redirectToProviderGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from facebook.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        $userSocial = Socialite::driver('facebook')->user();
        $user = User::where('email',$userSocial->user['email'])->first();
        if($user){
            if(Auth::LoginUsingId($user->id)){
                return redirect()->route('home');
            }
        }
        //else sign the user
       // dd($userSocial);
        $userSignUp = User::create([
            'name' => $userSocial->user['name'],
            'email' => $userSocial->user['email'],
            'avatar' => $userSocial->avatar,
            //'social_link' => $userSocial->link,
            //'gender' => $userSocial->gender,
            'password' => bcrypt(env('DEFAULT_PASSWORD')),
        ]);

        if($userSignUp) {
            if(Auth::LoginUsingId($userSignUp->id)){
                return redirect()->route('home');
            }
        }
        
    }

    public function handleProviderCallbackGoogle()
    {
        $userSocial = Socialite::driver('google')->user();

        
        $user = User::where('email',$userSocial->user['emails'][0]['value'])->first();
        if($user){
            if(Auth::LoginUsingId($user->id)){
                return redirect()->route('home');
            }
        }
        //else sign the user

        $userSignUp = User::create([
            'name' => $userSocial->user['displayName'],
            'email' => $userSocial->user['emails'][0]['value'],
            'social_link' => $userSocial->user['url'],
            'avatar' => $userSocial->user['image']['url'],
            'password' => bcrypt('Password1@'),
        ]);

        if($userSignUp) {
            if(Auth::LoginUsingId($userSignUp->id)){
                return redirect()->route('home');
            }
        }
        
    }



}
