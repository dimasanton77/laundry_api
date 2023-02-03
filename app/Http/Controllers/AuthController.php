<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;

use App\Models\User;
use App\Models\Member;


class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Register Function
     */
    public function register(Request $request)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {

            $input = $request->all();
    
            $validationRules = [
                'nama' => 'required|string|max:100',
                'alamat' => 'required|string',
                'no_hp' => 'required|string|max:20',
                'username' => 'required|string|unique:pengguna|max:100',
                'password' => 'required|confirmed|min:6',
            ];

            $validator = Validator::make($input, $validationRules);
    
            if ($validator->fails()) {
                return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
            }
            
            $user = new User(); 
            $user->nama = $input['nama'];
            $user->username = $input['username'];
            $user->role_id = 2;
            $plainPassword = $input['password'];
            $user->password = app('hash')->make($plainPassword);
    
            if ($user->save()) {

                $member = new Member();
                $member->pengguna_id = $user->id;
                $member->nama = $input['nama'];
                $member->alamat = $input['alamat'];
                $member->no_hp = $input['no_hp'];

                if ($member->save()) {
                    if ($acceptHeader === 'application/json') {
                        $response = [
                            'message' => 'Register Success',
                            'status_code' => Response::HTTP_CREATED,
                            'data' => $member
                        ];
                        return response()->json($response, Response::HTTP_CREATED);
                    
                    } else {
                        $xml = new \SimpleXMLElement('<member/>');
                        $xml->addChild('id', $member->id);
                        $xml->addChild('pengguna_id', $member->pengguna_id);    
                        $xml->addChild('nama', $member->nama);    
                        $xml->addChild('alamat', $member->alamat);  
                        $xml->addChild('no_hp', $member->no_hp);
                        return $xml->asXML();
                    }
                }
            }
    
            $response = [
                'message' => 'Register Failed',
                'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
            ];
    
            return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
        } else {
            $response = [
                'message' => 'Not Acceptable',
                'status_code' => Response::HTTP_NOT_ACCEPTABLE
            ];
    
            return response()->json($response, Response::HTTP_NOT_ACCEPTABLE);
        }
    }

    /**
     * Login Function
     */
    public function login(Request $request)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            $input = $request->all();
    
            $validationRules = [
                'username' => 'required|string',
                'password' => 'required|string'
            ];
    
            $validator = Validator::make($input, $validationRules);
    
            if ($validator->fails()) {
                return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
            }
    
            $credentials = $request->only(['username', 'password']);
    
            if (!$token = Auth::attempt($credentials)) {
                $response = [
                    'message' => 'Unauthorized',
                    'status_code' => Response::HTTP_UNAUTHORIZED
                ];
    
                return response()->json($response, Response::HTTP_UNAUTHORIZED);
            }
    
            $user = Auth::user();
            $tokenArr = [
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => Auth::factory()->getTTL() * 60
            ];
    
            $arrUser = json_decode(json_encode($user), true);
            $data = array_merge($arrUser, $tokenArr);
            
            if ($acceptHeader === 'application/json') {
                $response = [
                    'message' => 'Login Success',
                    'status_code' => Response::HTTP_ACCEPTED,
                    'data' => $data
                ];
        
                return response()->json($response, Response::HTTP_ACCEPTED);
            } else {
                $xml = new \SimpleXMLElement('<user-login/>');

                $xml->addChild('id', $user->id);
                $xml->addChild('role_id', $user->role_id);    
                $xml->addChild('nama', $user->nama);    
                $xml->addChild('username', $user->username);
                $xml->addChild('token', $tokenArr['token']);
                $xml->addChild('token_type', $tokenArr['token_type']);
                $xml->addChild('expires_in', $tokenArr['expires_in']);
                return $xml->asXML();
            }
            
        } else {
            $response = [
                'message' => 'Not Acceptable',
                'status_code' => Response::HTTP_NOT_ACCEPTABLE
            ];
    
            return response()->json($response, Response::HTTP_NOT_ACCEPTABLE);
        }
    }

    /**
     * Logout Function
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {

            if ($acceptHeader === 'application/json') {
                $response = [
                    'message' => 'Logout Success',
                    'status_code' => Response::HTTP_OK
                ];
        
                return response()->json($response, Response::HTTP_OK);
            } else {
                $xml = new \SimpleXMLElement('<user-logout/>');
                $xml->addChild('message', 'Logout Success');
                $xml->addChild('status_code', Response::HTTP_OK);    
                return $xml->asXML();
            }
        } else {
            $response = [
                'message' => 'Not Acceptable',
                'status_code' => Response::HTTP_NOT_ACCEPTABLE
            ];
    
            return response()->json($response, Response::HTTP_NOT_ACCEPTABLE);
        }
    }
}
