<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paket;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;

class PaketController extends Controller
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
     * Get Pakets Function
     * @return pakets
     */
    public function index(Request $request)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {

            $pakets = Paket::orderBy('id', 'ASC')->paginate()->toArray();
            
            if ($acceptHeader === 'application/json') {
                $response = [
                    'message' => 'Get Pakets Success',
                    'status_code' => Response::HTTP_OK,
                    'data' => [
                        'total' => $pakets['total'],
                        'limit' => $pakets['per_page'],
                        'pagination' => [
                            'next_page' => $pakets['next_page_url'],
                            'prev_page' => $pakets['prev_page_url'],
                            'current_page' => $pakets['current_page']
                        ],
                        'data' => $pakets['data']
                    ]
                ];
    
                return response()->json($response, Response::HTTP_OK);
            } else {
                $xml = new \SimpleXMLElement('<pakets/>');

                foreach ($pakets['data'] as $item) {
                    // create xml
                    $xmlItem = $xml->addChild('paket');
                    $xmlItem->addChild('id', $item['id']);
                    $xmlItem->addChild('nama', $item['nama']);
                    $xmlItem->addChild('lama_pengerjaan', $item['lama_pengerjaan']);
                    $xmlItem->addChild('jenis_pengerjaan', $item['jenis_pengerjaan']);
                    $xmlItem->addChild('jenis_cucian', $item['jenis_cucian']);
                    $xmlItem->addChild('harga', $item['harga']);
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
     * Show Paket Function
     */
    public function show(Request $request, $id)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            $paket = Paket::find($id);

            if (isset($paket)) {

                if ($acceptHeader === 'application/json') {
                    $response = [
                        'message' => 'Get Paket Success',
                        'status_code' => Response::HTTP_OK,
                        'data' => $paket
                    ];
        
                    return response()->json($response, Response::HTTP_OK);
                } else {
                    $xml = new \SimpleXMLElement('<paket/>');

                    $xml->addChild('id', $paket->id);
                    $xml->addChild('nama', $paket->nama);
                    $xml->addChild('lama_pengerjaan', $paket->lama_pengerjaan);
                    $xml->addChild('jenis_pengerjaan', $paket->jenis_pengerjaan);
                    $xml->addChild('jenis_cucian', $paket->jenis_cucian);
                    $xml->addChild('harga', $paket->harga);
                    return $xml->asXML();
                }

            } else {
                $response = [
                    'message' => 'Paket Not Found',
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
     * Create Paket Function
     */
    public function create(Request $request)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {

            $input = $request->all();

            $validationRules = [
                'nama' => 'required|string|max:100|unique:paket',
                'lama_pengerjaan' => 'required|integer',
                'jenis_pengerjaan' => 'required|string|max:200',
                'jenis_cucian' => 'required|string|max:100',
                'harga' => 'required|integer',
            ];

            $validator = Validator::make($input, $validationRules);

            if ($validator->fails()) {
                return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
            }

            $paket = new Paket();

            $paket->nama = $input['nama'];
            $paket->lama_pengerjaan = $input['lama_pengerjaan'];
            $paket->jenis_pengerjaan = $input['jenis_pengerjaan'];
            $paket->jenis_cucian = $input['jenis_cucian'];
            $paket->harga = $input['harga'];

            if ($paket->save()) {

                if ($acceptHeader === 'application/json') {
                    $response = [
                        'message' => 'Create Paket Success',
                        'status_code' => Response::HTTP_CREATED,
                        'data' => $paket
                    ];
        
                    return response()->json($response, Response::HTTP_CREATED);
                } else {
                    $xml = new \SimpleXMLElement('<paket/>');
                    $xml->addChild('id', $paket->id);
                    $xml->addChild('nama', $paket->nama);
                    $xml->addChild('lama_pengerjaan', $paket->lama_pengerjaan);
                    $xml->addChild('jenis_pengerjaan', $paket->jenis_pengerjaan);
                    $xml->addChild('jenis_cucian', $paket->jenis_cucian);
                    $xml->addChild('harga', $paket->harga);
                    return $xml->asXML();
                }
            } else {
                $response = [
                    'message' => 'Create Paket Failed',
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
     * Update Paket Function
     */
    public function update(Request $request, $id)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            $input = $request->all();

            $validationRules = [
                'nama' => 'required|string|max:50|unique:paket,nama,'. $id,
                'lama_pengerjaan' => 'required|integer',
                'jenis_pengerjaan' => 'required|string|max:200',
                'jenis_cucian' => 'required|string|max:100',
                'harga' => 'required|integer',
            ];

            $validator = Validator::make($input, $validationRules);

            if ($validator->fails()) {
                return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
            }
            
            $paket = Paket::find($id);

            if (isset($paket)) {
                $paket->nama = $input['nama'];
                $paket->lama_pengerjaan = $input['lama_pengerjaan'];
                $paket->jenis_pengerjaan = $input['jenis_pengerjaan'];
                $paket->jenis_cucian = $input['jenis_cucian'];
                $paket->harga = $input['harga'];

                if ($paket->save()) {

                    if ($acceptHeader === 'application/json') {
                        $response = [
                            'message' => 'Update Paket Success',
                            'status_code' => Response::HTTP_OK,
                            'data' => $paket
                        ];
            
                        return response()->json($response, Response::HTTP_OK);
                    } else {
                        $xml = new \SimpleXMLElement('<paket/>');
                        $xml->addChild('id', $paket->id);
                        $xml->addChild('nama', $paket->nama);
                        $xml->addChild('lama_pengerjaan', $paket->lama_pengerjaan);
                        $xml->addChild('jenis_pengerjaan', $paket->jenis_pengerjaan);
                        $xml->addChild('jenis_cucian', $paket->jenis_cucian);
                        $xml->addChild('harga', $paket->harga);
                        return $xml->asXML();
                    }
                } else {
                    $response = [
                        'message' => 'Create Paket Failed',
                        'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
                    ];
            
                    return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                $response = [
                    'message' => 'Paket Not Found',
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
     * Delete Paket Function
     */
    public function delete(Request $request, $id)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            $paket = Paket::find($id);

            if (isset($paket)) {
                if ($paket->delete()) {

                    if ($acceptHeader === 'application/json') {
                        $response = [
                            'message' => 'Delete Paket Success',
                            'status_code' => Response::HTTP_OK
                        ];
            
                        return response()->json($response, Response::HTTP_OK);
                    } else {
                        $xml = new \SimpleXMLElement('<paket/>');

                        $xml->addChild('message', 'Delete Paket Success');
                        $xml->addChild('status_code', Response::HTTP_OK);

                        return $xml->asXML();
                    }
                } else {
                    $response = [
                        'message' => 'Delete Paket Failed',
                        'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
                    ];
        
                    return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                $response = [
                    'message' => 'Paket Not Found',
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
