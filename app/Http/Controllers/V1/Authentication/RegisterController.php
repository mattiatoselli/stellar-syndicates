<?php

namespace App\Http\Controllers\V1\Authentication;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AuthenticationRegisterRequest;
use App\Models\User;

class RegisterController extends Controller
{
    /**
     * Register a new account.
     * Once you register a new account you will receive some credits to start.
     * The API returns the user's data, and your id_number. This is your token and should be provided in order to access to every
     * resource. Please note that there is no way of recovering your token, and that your token is the only way to identify you, so if you give your token to someone, that user will be able to act as you in Stellar Syndicates.
     * Tokens will never expire.
     * @bodyParam username string required. Your username. Example: iceman29
     * @responseFile storage/responses/register_user.json
     */
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