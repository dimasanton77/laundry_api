<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;

class RoleController extends Controller
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
     * Get Roles Function
     * @return roles
     */
    public function index(Request $request)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {

            $roles = Role::orderBy('id', 'ASC')->paginate()->toArray();
            
            if ($acceptHeader === 'application/json') {
                $response = [
                    'message' => 'Get Roles Success',
                    'status_code' => Response::HTTP_OK,
                    'data' => [
                        'total' => $roles['total'],
                        'limit' => $roles['per_page'],
                        'pagination' => [
                            'next_page' => $roles['next_page_url'],
                            'prev_page' => $roles['prev_page_url'],
                            'current_page' => $roles['current_page']
                        ],
                        'data' => $roles['data']
                    ]
                ];
    
                return response()->json($response, Response::HTTP_OK);
            } else {
                $xml = new \SimpleXMLElement('<roles/>');

                foreach ($roles['data'] as $item) {
                    // create xml
                    $xmlItem = $xml->addChild('role');

                    $xmlItem->addChild('id', $item['id']);
                    $xmlItem->addChild('nama', $item['nama']);
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
     * Show Role Function
     */
    public function show(Request $request, $id)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            $role = Role::find($id);

            if (isset($role)) {

                if ($acceptHeader === 'application/json') {
                    $response = [
                        'message' => 'Get Role Success',
                        'status_code' => Response::HTTP_OK,
                        'data' => $role
                    ];
        
                    return response()->json($response, Response::HTTP_OK);
                } else {
                    $xml = new \SimpleXMLElement('<role/>');

                    $xml->addChild('id', $role->id);
                    $xml->addChild('nama', $role->nama);

                    return $xml->asXML();
                }

            } else {
                $response = [
                    'message' => 'Role Not Found',
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
     * Create Role Function
     */
    public function create(Request $request)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {

            $input = $request->all();

            $validationRules = [
                'nama' => 'required|string|max:50|unique:role'
            ];

            $validator = Validator::make($input, $validationRules);

            if ($validator->fails()) {
                return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
            }

            $role = new Role();

            $role->nama = $input['nama'];

            if ($role->save()) {

                if ($acceptHeader === 'application/json') {
                    $response = [
                        'message' => 'Create Role Success',
                        'status_code' => Response::HTTP_CREATED,
                        'data' => $role
                    ];
        
                    return response()->json($response, Response::HTTP_CREATED);
                } else {
                    $xml = new \SimpleXMLElement('<role/>');

                    $xml->addChild('id', $role->id);
                    $xml->addChild('nama', $role->nama);
                    return $xml->asXML();
                }
            } else {
                $response = [
                    'message' => 'Create Role Failed',
                    'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
                ];
        
                return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
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
     * Update Role Function
     */
    public function update(Request $request, $id)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            $input = $request->all();

            $validationRules = [
                'nama' => 'required|string|max:50|unique:role,nama,'. $id
            ];

            $validator = Validator::make($input, $validationRules);

            if ($validator->fails()) {
                return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
            }
            
            $role = Role::find($id);

            if (isset($role)) {
                $role->nama = $input['nama'];

                if ($role->save()) {

                    if ($acceptHeader === 'application/json') {
                        $response = [
                            'message' => 'Update Role Success',
                            'status_code' => Response::HTTP_OK,
                            'data' => $role
                        ];
            
                        return response()->json($response, Response::HTTP_OK);
                    } else {
                        $xml = new \SimpleXMLElement('<role/>');

                        $xml->addChild('id', $role->id);
                        $xml->addChild('nama', $role->nama);

                        return $xml->asXML();
                    }
                } else {
                    $response = [
                        'message' => 'Create Role Failed',
                        'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
                    ];
            
                    return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                $response = [
                    'message' => 'Role Not Found',
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
     * Delete Role Function
     */
    public function delete(Request $request, $id)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            $role = Role::find($id);

            if (isset($role)) {
                if ($role->delete()) {

                    if ($acceptHeader === 'application/json') {
                        $response = [
                            'message' => 'Delete Role Success',
                            'status_code' => Response::HTTP_OK
                        ];
            
                        return response()->json($response, Response::HTTP_OK);
                    } else {
                        $xml = new \SimpleXMLElement('<role/>');

                        $xml->addChild('message', 'Delete Role Success');
                        $xml->addChild('status_code', Response::HTTP_OK);

                        return $xml->asXML();
                    }
                } else {
                    $response = [
                        'message' => 'Delete Role Success',
                        'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
                    ];
        
                    return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                $response = [
                    'message' => 'Role Not Found',
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
