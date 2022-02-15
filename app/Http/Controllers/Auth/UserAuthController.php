<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserAuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);

        $data['password'] = bcrypt($request->password);

        $user = User::create($data);

        $token = $user->createToken('API Token')->accessToken;

        return response([ 'user' => $user, 'token' => $token]);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);


        $credentials = request(['email', 'password']);
        $member = $this->retrieveByCredentials($credentials);
        if(!$member){
            return response()->json([
                'status' => 401,
                'messages' => [
                    'failed' => 'Sorry there is no data match, or your account is inactive'
                ]
            ])->setStatusCode(401, 'Sorry there is no data match, or your account is inactive');
        }
        $tokenResult = $member->createToken("Kalla Friends Access Client", ['*']);
        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();
        $token = [
            "access_token" => $tokenResult->accessToken,
            "token_type" => "Bearer",
            "expires_at" => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ];

        return response(['user' => $member, 'token' => $token]);

    }

	public function retrieveByCredentials (array $credentials) {
		$user = new User();
		foreach ($credentials as $credentialKey => $credentialValue) {
            if (!Str::contains($credentialKey, 'password')) {
				$user = $user->where($credentialKey, $credentialValue);
			}
        }
        $member = $user->select(
            'id',
            'name',
            'email')->first();
        // dd($this->getSql($user));
		return $member;
	}
}
