<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Notifications\ResetPass;
use Illuminate\Http\Request;
// use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification;

class SendMail extends Controller
{
    public function reset($email)
    {
        // $user=User::find($request);
        $user = User::where('email',$email)->first();
        // print_r($user);
        if (is_null($user)) {
        echo "sorry,this email is not found";
        }else{
            // Notification::send($user,new ResetPass());
            return $this->sendResponse($user, 'successfully.');
        }

    }


    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];


        return response()->json($response, 200);
    }

}
