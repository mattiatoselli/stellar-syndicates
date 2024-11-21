<?php

namespace App\Http\Controllers\V1\Authentication;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AuthenticationRegisterRequest;
use App\Models\User;

class RegisterController extends Controller
{
    public function __invoke(AuthenticationRegisterRequest $request)
    {
        $user = new User();
        $user->username = $request->input('username');
        $user->credits = 25000;
        $user->save();
        $token = $user->createToken($user->id)->plainTextToken;
        $response = array(
            "user" => $user,
            "id_number" => $token,
        );
        return $response;
    }
}