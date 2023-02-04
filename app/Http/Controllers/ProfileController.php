<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }


    /**
     * Show Profile Function
     */
    public function show(Request $request)
    {
        $id = Auth::user()->id;
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            if(Auth::user()->role_id == 1){
                $profile = User::with('role')->find($id);
            }else{
                $profile = User::with('member','role')->find($id);
            }
            if (isset($profile)) {

                if ($acceptHeader === 'application/json') {
                    $response = [
                        'message' => 'Get Profile Success',
                        'status_code' => Response::HTTP_OK,
                        'data' => $profile
                    ];
        
                    return response()->json($response, Response::HTTP_OK);
                } else {
                    $xmlItem = new \SimpleXMLElement('<prodile/>');

                    $xmlItem->addChild('id', $profile['id']);
                    $xmlItem->addChild('role_id', $profile['role_id']);
                    $xmlItem->addChild('nama', $profile['nama']);
                    $xmlItem->addChild('username', $profile['username']);

                    $xmlItemRole = $xmlItem->addChild('role');
                    $xmlItemRole->addChild('id', $profile['role']['id']);
                    $xmlItemRole->addChild('nama', $profile['role']['nama']);

                    if(Auth::user()->role_id == 2){
                        $xmlItemMember = $xmlItem->addChild('member');
                        $xmlItemMember->addChild('id', $profile['member']['id']);
                        $xmlItemMember->addChild('pengguna_id', $profile['member']['pengguna_id']);
                        $xmlItemMember->addChild('nama', $profile['member']['nama']);
                        $xmlItemMember->addChild('alamat', $profile['member']['alamat']);
                        $xmlItemMember->addChild('no_hp', $profile['member']['no_hp']);
                    }
                    return $xmlItem->asXML();
                }

            } else {
                $response = [
                    'message' => 'Profile Not Found',
                    'status_code' => Response::HTTP_NOT_FOUND
                ];
        
                return response()->json($response, Response::HTTP_NOT_FOUND);
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
     * Update Profile Function
     */
    public function update(Request $request)
    {
        $id = Auth::user()->id;
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            $input = $request->all();

            if(Auth::user()->role_id == 2){
                $validationRules = [
                    'nama' => 'required|string|max:100',
                    'alamat' => 'required|string',
                    'no_hp' => 'required|string|max:20',
                    'username' => 'required|string|max:100|unique:pengguna,username,'. $id,
                ];
            }else{
                $validationRules = [
                    'nama' => 'required|string|max:100',
                    'username' => 'required|string|max:100|unique:pengguna,username,'. $id,
                ];
            }

            $validator = Validator::make($input, $validationRules);

            if ($validator->fails()) {
                return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
            }
            
            $user = User::find($id);

            if (isset($user)) {
                if(Auth::user()->role_id == 2){
                    $member = Member::where('pengguna_id',Auth::user()->id)->first();
                    $memberId = $member->id;
                    $profile = Member::find($memberId);

                    $profile->nama = $input['nama'];
                    $profile->alamat = $input['alamat'];
                    $profile->no_hp = $input['no_hp'];    
                    $profile->save();
                }
                

                $user->nama = $input['nama'];
                $user->username = $input['username'];
                if(!empty($input['password'])){
                    $plainPassword = $input['password'];
                    $user->password = app('hash')->make($plainPassword);
                }

                if($user->save()){
                    if ($acceptHeader === 'application/json') {
                        $response = [
                            'message' => 'Update Profile Success',
                            'status_code' => Response::HTTP_OK,
                            'data' => $user
                        ];
            
                        return response()->json($response, Response::HTTP_OK);
                    } else {
                        $xml = new \SimpleXMLElement('<prodile/>');
                        $xml->addChild('id', $user->id);
                        $xml->addChild('role_id', $user->role_id);    
                        $xml->addChild('nama', $user->nama);    
                        $xml->addChild('username', $user->username);  
                        return $xml->asXML();
                    }
                }
            } else {
                $response = [
                    'message' => 'Profile Not Found',
                    'status_code' => Response::HTTP_NOT_FOUND
                ];
        
                return response()->json($response, Response::HTTP_NOT_FOUND);
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
