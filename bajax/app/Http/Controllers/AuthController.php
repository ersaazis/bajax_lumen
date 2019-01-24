<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use Validator;

class AuthController extends Controller
{
    public function __construct(){
        $this->middleware('auth', ['only' => ['logout']]);
    }
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'email' => 'required|email|max:191|unique:users',
            'password' => 'required|min:8',
            'birthplace' => 'required|max:191', 
            'dateofbirth' => 'required|date_format:Y-m-d', 
            'address' => 'required',

        ]);
        if ($validator->fails()){
            return response()->json([
                'success' => false,
                'messages' => 'Register Fail !',
                'data' => $validator->errors(),
            ], 400);
        }

        $data = $request->all();
        $register=User::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),

            'name' => $data['name'],
            'birthplace' => $data['birthplace'],
            'dateofbirth' => $data['dateofbirth'],
            'aboutme' => $data['aboutme'],
            'address' => $data['address'],
            'website' => $data['website'],
        ]);

        if($register){
            return response()->json([
                'success' => true,
                'messages' => 'Register Success !',
                'data' => $register
            ], 201);
        }
        else {
            return response()->json([
                'success' => false,
                'messages' => 'Register Fail !',
                'data' => ''
            ], 400);
        }
    }
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:191',
            'password' => 'required',
        ]);
        if ($validator->fails()){
            return response()->json([
                'success' => false,
                'messages' => 'Login Fail !',
                'data' => $validator->errors(),
            ], 400);
        }

        $email = $request->input('email');
        $password = $request->input('password');

        $user = User::where('email', $email)->first();
        if(!empty($user) and Hash::check($password, $user->password)){
            $apiToken=base64_encode(str_random(40));
            $user->update([
                'api_token' => $apiToken
            ]);

            return response()->json([
                'success' => true,
                'messages' => 'Login Success !',
                'data' => [
                    'user' => $user,
                    'api_token' => $apiToken
                ]
            ], 201);
        }
        else {
            return response()->json([
                'success' => false,
                'messages' => 'Login Fail !',
                'data' => ''
            ], 400);
        }
    }
    public function logout(Request $request){
        $api_token= $request->header('Authorization');
        $user = User::where('api_token', $api_token)->first();;
        $user->update([
            'api_token' => ''
        ]);
        return response()->json([
            'success' => true,
            'messages' => 'Logout Success !',
            'data' => ''
        ], 200);
    }
}