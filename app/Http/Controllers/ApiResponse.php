<?php

namespace App\Http\Controllers;

class ApiResponse extends Controller
{
    public function apiResponse( $type , $data, $message , $code){


        if($type == ResultType::Success){
            $response['data'] = $data;
            $response['isSuccess'] = true;
        }
        else{
            $response['errors'] = $data;
            $response['isSuccess'] = false;
        }


        $response['message'] = $message;

        return response() -> json([
            $response
        ], $code);
    }
}

Class ResultType {

    const Success = 1;
    const Error = 2;


}
