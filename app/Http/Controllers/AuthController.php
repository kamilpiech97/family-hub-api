<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\User;
use App\Family;
use App\Member;
use App\Notifications\SignupActivate;
use App\Http\Requests\StoreUser;
use App\Http\Requests\LoginUser;
use App\Services\SpamChecker;
use App\Services\SignupService;
use App\Services\SigninService;
use App\Jobs\AfterActivateAccount;


class AuthController extends Controller
{
    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function signup(StoreUser $request,SignupService $singup)
    {
        $singup->register($request);
        return response()->json([
            'message' => 'Successfully created user and now active your account at mail!'
        ], 201);
    }

    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(LoginUser $request,SigninService $singin)
    {
        $data = $singin->login($request);
        return $data;
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        
        Log::channel()->notice("User ".Auth::user()->id." logout");

        return response()->json([
            'message' => 'Successfully logged out'
        ],200);
    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function spamChecker(SpamChecker $service)
    {   
        $service = $service->check();

        return response()->json([
            'message' => 'Scan done, '.$service.' users deleted!',
        ], 200 );
    }

    public function signupActivate($token)
    {
        $user = User::where('activation_token', $token)->first();
        if (!$user) {
            return response()->json([
                'message' => 'This activation token is invalid.'
            ], 404);
        }
        $user->active = true;
        $user->activation_token = '';
        $user->save();
        
        AfterActivateAccount::dispatch($user);
        
        Log::channel()->notice("User ".$user->id." activated account");
        return response()->json(['message' => 'Activated!','data' => $user], 200 );
    }
}
