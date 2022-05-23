<?php

namespace App\Http\Controllers;

use App\ContentTypeChecker;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AutenticationController extends Controller
{

    // ova je funkcija se korisiti kreirati dummy knjižničare
    public function createNewUser(Request $request) {
        $requestData = $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required'
        ]);

        $user = User::create([
            'name' => $requestData['name'],
            'email' => $requestData['email'],
            'password' => bcrypt($requestData['password'])
        ]);

        $token = $user->createToken('token_key')->plainTextToken;

        $user_credentials = ['user' => $user, 'token' => $token];

        return response($user_credentials, 201);
    }

    public function login(Request $request) {
        $contentChecker = new ContentTypeChecker();
        if($contentChecker->isNotApplicationJsonContentType($request)){
            $response = $contentChecker->setWrongContentTypeResponse();
            return $response;
        }

        $data_json = $request->instance()->getContent();
        $data = json_decode($data_json);
        $email = $data->email;
        $password = $data->password;

        if(empty($email) || empty($password)) {
            return response(['message' => 'Email and password must be provided'], 400);
        }

        $user = User::where('email', $email)->first();

        if(!$user || !Hash::check($password, $user->password)) {
            return response(['message' => 'Username or password is not correct'], 401);
        }

        $token = $user->createToken('token_key')->plainTextToken;
        $user_credentials = ['user' => $user, 'token' => $token];
        return response($user_credentials, 201);
    }

    public function logout(Request $request) {
        $contentChecker = new ContentTypeChecker();
        if($contentChecker->isNotApplicationJsonContentType($request)){
            $response = $contentChecker->setWrongContentTypeResponse();
            return $response;
        }
        auth()->user()->tokens()->delete();
        return  response(['message' => 'Logged out'],200);
    }
}
