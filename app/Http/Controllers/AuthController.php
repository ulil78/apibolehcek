<?php

namespace App\Http\Controllers;
use Tymon\JWTAuth\Exceptions\JWTException;

use Illuminate\Http\Request;
use App\User;
use JWTAuth;

class AuthController extends Controller
{
    public function postSignup(Request $request)
    {
        $this->validate($request, [

            'name'      => 'required',
            'email'     => 'required|unique:users',
            'password'  => 'required',

        ]);

        return User::create([
          'name'      => $request->json('name'),
          'email'     => $request->json('email'),
          'password'  => bcrypt($request->json('password')),
        ]);


    }

    public function postSignin(Request $request)
    {

      $this->validate($request, [

          'email'     => 'required',
          'password'  => 'required',

      ]);

      // grab credentials from the request
        $credentials = $request->only('email', 'password');

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // all good so return the token
        return response()->json([
          'id'    => $request->user()->id,
          'name'    => $request->user()->name,
          'email'    => $request->user()->email,
          'token' => $token
        ]);



    }
}
