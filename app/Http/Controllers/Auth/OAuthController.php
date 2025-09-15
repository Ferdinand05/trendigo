<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

use function Laravel\Prompts\password;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    public function authRedirect()
    {
        return Socialite::driver('google')->redirect();
    }


    public function authCallback()
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::where('oauth_id', $googleUser->id)->first();
        if (!$user) {

            $userEmail = User::where('email', $googleUser->email)->first();

            if ($userEmail) {
                $userEmail->update([
                    'oauth_id' => $googleUser->id,
                    'oauth_token' => $googleUser->token,
                    'oauth_refresh_token' => $googleUser->refreshToken,
                    'email_verified_at' => $userEmail->email_verified_at ?? Carbon::now(),
                ]);

                $user = $userEmail;
            } else {
                $user = User::create([
                    'oauth_id' => $googleUser->id,
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'oauth_token' => $googleUser->token,
                    'oauth_refresh_token' => $googleUser->refreshToken,
                    'password' => Str::password(12, true, true, false, false),
                    'email_verified_at' => Carbon::now(),
                    'role_id' => 3 //  role customer
                ]);
            }
        }


        Auth::login($user);

        return to_route('home');
    }
}
