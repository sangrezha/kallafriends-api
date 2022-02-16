<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\MemberAsset;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserAuthController extends Controller
{
    public function register(Request $request)
    {
        $request->request->add([
            'name' => $request->p_name,
            'email' => $request->p_email,
            'password' =>  $request->p_password,
            'mobile' =>  $request->p_mobile
        ]); 
        $data = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:kfr_member',
            'password' => 'required'
        ]);

        $file_data = $request->img;
        $file_name = 'image_'.time().'.png';
        if($file_data!=""){
          Storage::disk('public')->put($file_name,base64_decode($file_data));     
        }
        exit;

        $member = new User();
        $member->id = rand(1, 100).rand(1, 100).rand(1, 100).rand(1, 100).date("dmYHis");
        $member->mobile = $request->p_mobile;
        $member->name = $request->p_name;
        $member->email = $request->p_email;
        $member->passwordhash = md5(serialize($request->password));
        $member->status = 'active';
        $member->post_date = Carbon::now();
        $member->id_facebook = $request->p_facebook;
        $member->id_google = $request->p_google;
        $member->id_apple = $request->p_apple;
        $member->auth_apple_code = $request->p_authorizationCode;
        $member->save();

        // $token = $user->createToken('API Token')->accessToken;
        $tokenResult = $member->createToken("Kalla Friends Access Client", ['*']);
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();
        $token = [
            "access_token" => $tokenResult->accessToken,
            "token_type" => "Bearer",
            "expires_at" => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ];
        $data = $member;
        $data['token'] = $token;
        $responseArr['status'] = 200;
        $responseArr['messages'] = "SUCCESS";
        $responseArr['data'] = $data;
        return response($responseArr);
    }

    public function login(Request $request)
    {
        $responseArr = array('messages' => '', 'status' => false);
        $request->validate([
            'p_email' => 'email|required',
            'p_password' => 'required'
        ]);
        $request->request->add(['email' =>  $request->p_email, 'password' =>  $request->p_password]); 
        $credentials = request(['email', 'password']);
        $member = $this->retrieveByCredentials($credentials);
        if(!$member){
            return response()->json([
                'status' => 401,
                'messages' => 'Sorry there is no data match, or your account is inactive'
            ])->setStatusCode(401, 'Sorry there is no data match, or your account is inactive');
        }
        $tokenResult = $member->createToken("Kalla Friends Access Client", ['*']);
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();
        $token = [
            "access_token" => $tokenResult->accessToken,
            "token_type" => "Bearer",
            "expires_at" => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ];

        $data = $member->toArray();
        // if($member->communitymember == "active"){
        //     $data['member_text'] = "Anda saat ini telah menjadi Komunitas. \r\nSelamat menikmati fitur-fitur Anda sebagai Komunitas.";
        //     $data['who_am_i'] = "komunitas";
        //     $data['captionmember'] = "Komunitas";
        // }elseif($member->validmember == "active"){
        //     $data['member_text'] = "Anda saat ini telah menjadi Sohib Kalla Friends. \r\nSelamat menikmati fitur-fitur Anda sebagai Sohib Kalla Friends.";
        //     $data['who_am_i'] = "sohib";
        //     $data['captionmember'] = "Sohib Kalla Friends";
        // }else{
        //     $data['member_text'] = "Anda saat ini telah menjadi Teman Kalla Friends. \r\nSelamat menikmati fitur-fitur Anda sebagai Teman Kalla Friends.";
        //     $data['who_am_i'] = "teman";
        //     $data['captionmember'] = "Teman Kalla Friends";
        // }
        
        // $province = DB::table('kfr_province')->select('name')->where('id', $member->id_province)->first();
        // $point = MemberAsset::select(DB::raw('current_value+credit-debet as point'))->orderBy('post_date', 'DESC')->firstWhere('id_member', $member->id);

        // $data['call_via'] = json_decode($data['call_via'],true);
        // $data['nama_provinsi'] = optional($province)->name;
        // $data['point'] = (int) optional($point)->point;
        $data['token'] = $token;
        $responseArr['data'] = $data;
        $responseArr['status'] = 200;
        $responseArr['messages'] = "SUCCESS";
        return response($responseArr);

    }

	public function retrieveByCredentials (array $credentials) {
		$user = new User();
		foreach ($credentials as $credentialKey => $credentialValue) {
            if ($credentialKey == 'password') {
                $hashedPassword = md5(serialize($credentialValue));
				$user = $user->where('passwordhash', $hashedPassword);
			}else{
				$user = $user->where($credentialKey, $credentialValue);
            }
        }
        $member = $user->select(
            // 'customer_id',
            // 'type',
            'name',
            'email',
            // 'dob',
            // 'id_type',
            // 'id_number',
            // 'id_province',
            // 'id_city',
            // 'address',
            // 'mobile',
            'phone',
            'sex as gender',
            // 'occupation',
            // 'religion',
            // 'relation',
            // 'hobby',
            // 'socmed',
            // 'facebook',
            // 'twitter',
            // 'instagram',
            // 'google',
            // 'taukalla',
            // 'multi_car',
            // 'car',
            // 'other_brand',
            // 'other_car',
            // 'car_year',
            // 'car_nopol',
            // 'car_norangka',
            // 'call_via',
            // 'call_when',
            // 'call_time',
            // 'community',
            // 'id_community',
            // 'id_community_anggota',
            'picture as avatar',
            // 'status',
            // 'validmember',
            // 'communitymember',
            // 'point_profile',
            // 'reset_token',
            // 'reset_expired',
            // 'stnk',
            // 'id_facebook',
            // 'id_google',
            // 'id_apple',
            // 'auth_apple_code',
            'id'
        )->first();
        // dd($this->getSql($user));
		return $member;
	}
}
