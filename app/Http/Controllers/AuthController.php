<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\LogoutRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Mockery\Exception;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AuthController extends ApiResponse
{

    public function register(RegisterRequest $request){

        try {
            $user = new User();
            $user -> name = $request -> name;
            $user -> email = $request->email;
            $user -> password = Hash::make($request->password);
            $user -> save();

            return $this->apiResponse(ResultType::Success,$user, 'Kayıt Oluşturuldu', 200);

        }catch (Exception $e){

            return $this->apiResponse(ResultType::Error, $e,'Database Error', 404);
        }
    }

    public function login (LoginRequest $request){

        $user = User::where('email', $request->email)->get()->first();

        if(!$user){
            return $this->apiResponse(ResultType::Error,'Böyle Bir kayıt yok', 'User Not found', 404);
        }

        if(!Auth::attempt(request()->only('email', 'password'))){
            return $this->apiResponse(ResultType::Error,'Hatalı Bir Şifre Girdiniz', 'Hatalı Şifre', 200);
        }

        $token = $user -> createToken('token')->accessToken;
        $token -> expires_at = Carbon::now()->addMinute(2);

        $token->save();

        $data = ['user' => $user, 'token'=> $token];

        return $this->apiResponse(ResultType::Success,$data, 'Giriş Başarılı', 200);

    }

    public function logout(LogoutRequest $request){
        $token = $request->token;

        $result = DB::table('personal_access_tokens')->where('token', $token)->delete();

        return $this->apiResponse(ResultType::Success,  $result, 'Çıkış İşlemi Başarılı', 200);


    }
}
