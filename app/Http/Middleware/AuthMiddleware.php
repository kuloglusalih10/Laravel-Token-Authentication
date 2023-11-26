<?php

namespace App\Http\Middleware;

use App\Http\Controllers\ApiResponse;
use App\Http\Controllers\ResultType;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request -> bearerToken();
        if(!$token){
            return (new ApiResponse())->apiResponse(ResultType::Error, 'Önce Giriş Yapmalısınız', 'Token Bulunamadı', 400);
        }
        $token_row = DB::table('personal_access_tokens') -> where('token', $token)->get()-> first();

        if($token_row && $token_row->expires_at && !Carbon::now()->gt($token_row -> expires_at)){

            DB::table('personal_access_tokens') -> where('token', $token)->update(['expires_at' => Carbon::now()->addHour(1)]);
            return $next($request);
        }else{

            DB::table('personal_access_tokens') -> where('token', $token)->delete();
            return (new ApiResponse())->apiResponse(ResultType::Error, 'Tekra Giriş Yapmalısınız', 'Token Süresi Dolmuş', 400);
        }





    }
}
