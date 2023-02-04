<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;

class MemberController extends Controller
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
     * Get Members Function
     * @return member
     */
    public function index(Request $request)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {

            $member = Member::with('pengguna','pengguna.role')->orderBy('id', 'ASC')->paginate()->toArray();
            
            if ($acceptHeader === 'application/json') {
                $response = [
                    'message' => 'Get Members Success',
                    'status_code' => Response::HTTP_OK,
                    'data' => [
                        'total' => $member['total'],
                        'limit' => $member['per_page'],
                        'pagination' => [
                            'next_page' => $member['next_page_url'],
                            'prev_page' => $member['prev_page_url'],
                            'current_page' => $member['current_page']
                        ],
                        'data' => $member['data']
                    ]
                ];
    
                return response()->json($response, Response::HTTP_OK);
            } else {
                $xml = new \SimpleXMLElement('<members/>');

                foreach ($member['data'] as $item) {
                    // create xml
                    $xmlItem = $xml->addChild('member');

                    $xmlItem->addChild('id', $item['id']);
                    $xmlItem->addChild('pengguna_id', $item['pengguna_id']);
                    $xmlItem->addChild('nama', $item['nama']);
                    $xmlItem->addChild('alamat', $item['alamat']);
                    $xmlItem->addChild('no_hp', $item['no_hp']);

                    $xmlItemPengguna = $xmlItem->addChild('pengguna');
                    $xmlItemPengguna->addChild('id', $item['pengguna']['id']);
                    $xmlItemPengguna->addChild('role_id', $item['pengguna']['role_id']);
                    $xmlItemPengguna->addChild('nama', $item['pengguna']['nama']);
                    $xmlItemPengguna->addChild('username', $item['pengguna']['username']);

                    $xmlItemRole = $xmlItemPengguna->addChild('role');
                    $xmlItemRole->addChild('id', $item['pengguna']['role']['id']);
                    $xmlItemRole->addChild('nama', $item['pengguna']['role']['nama']);
                    
                }

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
     * Show Member Function
     */
    public function show(Request $request, $id)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            $member = Member::with('pengguna','pengguna.role')->find($id);

            if (isset($member)) {

                if ($acceptHeader === 'application/json') {
                    $response = [
                        'message' => 'Get Member Success',
                        'status_code' => Response::HTTP_OK,
                        'data' => $member
                    ];
        
                    return response()->json($response, Response::HTTP_OK);
                } else {
                    $xmlItem = new \SimpleXMLElement('<member/>');

                    $xmlItem->addChild('id', $member['id']);
                    $xmlItem->addChild('pengguna_id', $member['pengguna_id']);
                    $xmlItem->addChild('nama', $member['nama']);
                    $xmlItem->addChild('alamat', $member['alamat']);
                    $xmlItem->addChild('no_hp', $member['no_hp']);

                    $xmlItemPengguna = $xmlItem->addChild('pengguna');
                    $xmlItemPengguna->addChild('id', $member['pengguna']['id']);
                    $xmlItemPengguna->addChild('role_id', $member['pengguna']['role_id']);
                    $xmlItemPengguna->addChild('nama', $member['pengguna']['nama']);
                    $xmlItemPengguna->addChild('username', $member['pengguna']['username']);

                    $xmlItemRole = $xmlItemPengguna->addChild('role');
                    $xmlItemRole->addChild('id', $member['pengguna']['role']['id']);
                    $xmlItemRole->addChild('nama', $member['pengguna']['role']['nama']);
                    return $xmlItem->asXML();
                }

            } else {
                $response = [
                    'message' => 'Member Not Found',
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
     * Create Member Function
     */
    public function create(Request $request)
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
                            'message' => 'Create Member Success',
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
                'message' => 'Create Member Failed',
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
     * Update Member Function
     */
    public function update(Request $request, $id)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            $input = $request->all();

            $validationRules = [
                'nama' => 'required|string|max:100',
                'alamat' => 'required|string',
                'no_hp' => 'required|string|max:20',
                'username' => 'required|string|max:100|unique:pengguna,username,'. $id,
            ];

            $validator = Validator::make($input, $validationRules);

            if ($validator->fails()) {
                return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
            }
            
            $member = Member::find($id);

            if (isset($member)) {
                $member->nama = $input['nama'];
                $member->alamat = $input['alamat'];
                $member->no_hp = $input['no_hp'];

                if ($member->save()) {

                    $user = User::find($member->pengguna_id);

                    $user->nama = $input['nama'];
                    $user->username = $input['username'];
                    if(!empty($input['password'])){
                        $plainPassword = $input['password'];
                        $user->password = app('hash')->make($plainPassword);
                    }

                    if($user->save()){
                        if ($acceptHeader === 'application/json') {
                            $response = [
                                'message' => 'Update Member Success',
                                'status_code' => Response::HTTP_OK,
                                'data' => $member
                            ];
                
                            return response()->json($response, Response::HTTP_OK);
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
                } else {
                    $response = [
                        'message' => 'Create Member Failed',
                        'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
                    ];
            
                    return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                $response = [
                    'message' => 'Member Not Found',
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
     * Delete Member Function
     */
    public function delete(Request $request, $id)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            $member = Member::find($id);
            $pengguna_id = $member->pengguna_id;
            if (isset($member)) {
                if ($member->delete()) {

                    $pengguna = User::find($pengguna_id);
                    if (isset($pengguna)) {
                        if ($pengguna->delete()) {
                            if ($acceptHeader === 'application/json') {
                                $response = [
                                    'message' => 'Delete Member Success',
                                    'status_code' => Response::HTTP_OK
                                ];
                    
                                return response()->json($response, Response::HTTP_OK);
                            } else {
                                $xml = new \SimpleXMLElement('<member/>');
        
                                $xml->addChild('message', 'Delete Member Success');
                                $xml->addChild('status_code', Response::HTTP_OK);
        
                                return $xml->asXML();
                            }
                        }
                    }
                } else {
                    $response = [
                        'message' => 'Delete Member Failed',
                        'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
                    ];
        
                    return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                $response = [
                    'message' => 'Member Not Found',
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
