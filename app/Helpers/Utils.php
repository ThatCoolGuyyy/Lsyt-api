<?php

namespace App\Helpers;

use Exception;
use Validator;
use App\Exceptions\ValidationException;

class Utils
{
    public static function setResponse($status, $data = null, $message = null, $error=null, $custom_errors=null, $extraHeaders = [], $meta=[]){

        $response = ['status_code' => $status];
        $data = request()->base64_response ? base64_encode(json_encode($data ?? [])) : $data;

        !is_null($data) && $response['data'] = $data;
        !is_null($message) && $response['message'] = $message;
       
        $response = request()->base64_body ? base64_encode(json_encode($response)) : $response;

        return response()->json(
            $response, $status
        );
    }

    public function validate($requestBody, $rules, $message = null)
    {
        if ($requestBody === null) {
            throw new Exception('Request body cannot be null');
        }
    
        $validator = Validator::make($requestBody, $rules);
        if ($validator->fails()) {
            \Log::error($validator->getMessageBag()->toArray());
            throw new ValidationException(json_encode($validator->getMessageBag()->toArray()), $message);
        }
    }
   
}