<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Paket;
use App\Models\Member;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;

class TransaksiController extends Controller
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
     * Get Transaksis Function
     * @return transaksis
     */
    public function index(Request $request)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {

            if(Auth::user()->role_id == 1){
                $transaksis = Transaksi::with('member','paket')->where('status_cucian','proses')->orderBy('id', 'ASC')->paginate()->toArray();
            }else{
                $member = Member::where('pengguna_id',Auth::user()->id)->first();
                $memberId = $member->id;
                $transaksis = Transaksi::with('member','paket')->where('status_cucian','proses')->where('member_id',$memberId)->orderBy('id', 'ASC')->paginate()->toArray();
            }
            
            if ($acceptHeader === 'application/json') {
                $response = [
                    'message' => 'Get Transaksis Success',
                    'status_code' => Response::HTTP_OK,
                    'data' => [
                        'total' => $transaksis['total'],
                        'limit' => $transaksis['per_page'],
                        'pagination' => [
                            'next_page' => $transaksis['next_page_url'],
                            'prev_page' => $transaksis['prev_page_url'],
                            'current_page' => $transaksis['current_page']
                        ],
                        'data' => $transaksis['data']
                    ]
                ];
    
                return response()->json($response, Response::HTTP_OK);
            } else {
                $xml = new \SimpleXMLElement('<transaksis/>');

                foreach ($transaksis['data'] as $item) {
                    // create xml
                    $xmlItem = $xml->addChild('transaksi');
                    $xmlItem->addChild('id', $item['id']);
                    $xmlItem->addChild('member_id', $item['member_id']);
                    $xmlItem->addChild('berat', $item['berat']);
                    $xmlItem->addChild('tgl_mulai', $item['tgl_mulai']);
                    $xmlItem->addChild('tgl_selesai', $item['tgl_selesai']);
                    $xmlItem->addChild('keterangan', $item['keterangan']);
                    $xmlItem->addChild('status_pembayaran', $item['status_pembayaran']);
                    $xmlItem->addChild('status_cucian', $item['status_cucian']);
                    $xmlItem->addChild('status_pembayaran', $item['status_pembayaran']);
                    $xmlItem->addChild('total_harga', $item['total_harga']);
                    $xmlItemMember = $xmlItem->addChild('member');
                    $xmlItemMember->addChild('id', $item['member']['id']);
                    $xmlItemMember->addChild('pengguna_id', $item['member']['pengguna_id']);    
                    $xmlItemMember->addChild('nama', $item['member']['nama']);    
                    $xmlItemMember->addChild('alamat', $item['member']['alamat']);  
                    $xmlItemMember->addChild('no_hp', $item['member']['no_hp']);
                    $xmlItemPaket = $xmlItem->addChild('paket');
                    $xmlItemPaket->addChild('id', $item['paket']['id']);
                    $xmlItemPaket->addChild('nama', $item['paket']['nama']);    
                    $xmlItemPaket->addChild('lama_pengerjaan', $item['paket']['lama_pengerjaan']);    
                    $xmlItemPaket->addChild('jenis_pengerjaan', $item['paket']['jenis_pengerjaan']);  
                    $xmlItemPaket->addChild('jenis_cucian', $item['paket']['jenis_cucian']);
                    $xmlItemPaket->addChild('harga', $item['paket']['harga']);
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
     * Show Transaksi Function
     */
    public function show(Request $request, $id)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            if(Auth::user()->role_id == 1){
                $transaksi = Transaksi::with('member','paket')->where('status_cucian','proses')->find($id);
            }else{
                $member = Member::where('pengguna_id',Auth::user()->id)->first();
                $memberId = $member->id;
                $transaksi = Transaksi::with('member','paket')->where('status_cucian','proses')->where('member_id',$memberId)->find($id);

            }
            if (isset($transaksi)) {

                if ($acceptHeader === 'application/json') {
                    $response = [
                        'message' => 'Get Transaksi Success',
                        'status_code' => Response::HTTP_OK,
                        'data' => $transaksi
                    ];
        
                    return response()->json($response, Response::HTTP_OK);
                } else {
                    $xmlItem = new \SimpleXMLElement('<transaksi/>');
                    $xmlItem->addChild('id', $transaksi['id']);
                    $xmlItem->addChild('member_id', $transaksi['member_id']);
                    $xmlItem->addChild('berat', $transaksi['berat']);
                    $xmlItem->addChild('tgl_mulai', $transaksi['tgl_mulai']);
                    $xmlItem->addChild('tgl_selesai', $transaksi['tgl_selesai']);
                    $xmlItem->addChild('keterangan', $transaksi['keterangan']);
                    $xmlItem->addChild('status_pembayaran', $transaksi['status_pembayaran']);
                    $xmlItem->addChild('status_cucian', $transaksi['status_cucian']);
                    $xmlItem->addChild('status_pembayaran', $transaksi['status_pembayaran']);
                    $xmlItem->addChild('total_harga', $transaksi['total_harga']);
                    $xmlItemMember = $xmlItem->addChild('member');
                    $xmlItemMember->addChild('id', $transaksi['member']['id']);
                    $xmlItemMember->addChild('pengguna_id', $transaksi['member']['pengguna_id']);    
                    $xmlItemMember->addChild('nama', $transaksi['member']['nama']);    
                    $xmlItemMember->addChild('alamat', $transaksi['member']['alamat']);  
                    $xmlItemMember->addChild('no_hp', $transaksi['member']['no_hp']);
                    $xmlItemPaket = $xmlItem->addChild('paket');
                    $xmlItemPaket->addChild('id', $transaksi['paket']['id']);
                    $xmlItemPaket->addChild('nama', $transaksi['paket']['nama']);    
                    $xmlItemPaket->addChild('lama_pengerjaan', $transaksi['paket']['lama_pengerjaan']);    
                    $xmlItemPaket->addChild('jenis_pengerjaan', $transaksi['paket']['jenis_pengerjaan']);  
                    $xmlItemPaket->addChild('jenis_cucian', $transaksi['paket']['jenis_cucian']);
                    $xmlItemPaket->addChild('harga', $transaksi['paket']['harga']);
                    return $xmlItem->asXML();
                }

            } else {
                $response = [
                    'message' => 'Transaksi Not Found',
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
     * Create Transaksi Function
     */
    public function create(Request $request)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {

            $input = $request->all();

            $memberId = "";

            if(Auth::user()->role_id == 1){
                $memberId =  'required|integer';
            }

            $validationRules = [
                'member_id' => $memberId,
                'paket_id' => 'required|integer',
                'berat' => 'required|integer',
                'tgl_mulai' => 'required',
                'tgl_selesai' => 'required',
            ];

            $validator = Validator::make($input, $validationRules);

            if ($validator->fails()) {
                return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
            }

            $transaksi = new Transaksi();

            if(Auth::user()->role_id == 2){
                $member = Member::where('pengguna_id',Auth::user()->id)->first();
                $transaksi->member_id = $member->id;
            }else{
                $transaksi->member_id = $input['member_id'];
            }
            $transaksi->paket_id = $input['paket_id'];
            $transaksi->berat = $input['berat'];
            $transaksi->tgl_mulai = $input['tgl_mulai'];
            $transaksi->tgl_selesai = $input['tgl_selesai'];
            $transaksi->keterangan = $input['keterangan'];
            $transaksi->status_pembayaran = 'belum_lunas';
            $transaksi->status_cucian = 'proses';

            $paket = Paket::find($input['paket_id']);
            $total_harga = $paket->harga * $input['berat'];
            $transaksi->total_harga = $total_harga;


            if(Auth::user()->role_id == 1){

                if(!empty($input['status_pembayaran'])){
                    $transaksi->status_pembayaran = $input['status_pembayaran'];
                }

                if(!empty($input['status_cucian'])){
                    $transaksi->status_cucian = $input['status_cucian'];
                }
            }


            if ($transaksi->save()) {

                if ($acceptHeader === 'application/json') {
                    $response = [
                        'message' => 'Create Transaksi Success',
                        'status_code' => Response::HTTP_CREATED,
                        'data' => $transaksi
                    ];
        
                    return response()->json($response, Response::HTTP_CREATED);
                } else {
                    $xml = new \SimpleXMLElement('<transaksi/>');
                    $xml->addChild('id', $transaksi->id);
                    $xml->addChild('member_id', $transaksi->member_id);
                    $xml->addChild('berat', $transaksi->berat);
                    $xml->addChild('tgl_mulai', $transaksi->tgl_mulai);
                    $xml->addChild('tgl_selesai', $transaksi->tgl_selesai);
                    $xml->addChild('keterangan', $transaksi->keterangan);
                    $xml->addChild('status_pembayaran', $transaksi->status_pembayaran);
                    $xml->addChild('status_cucian', $transaksi->status_cucian);
                    $xml->addChild('status_pembayaran', $transaksi->status_pembayaran);
                    $xml->addChild('total_harga', $transaksi->total_harga);
                    return $xml->asXML();
                }
            } else {
                $response = [
                    'message' => 'Create Transaksi Failed',
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
     * Update Transaksi Function
     */
    public function update(Request $request, $id)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            $input = $request->all();

            $validationRules = [
                'member_id' => 'required|integer',
                'paket_id' => 'required|integer',
                'berat' => 'required|integer',
                'tgl_mulai' => 'required',
                'tgl_selesai' => 'required',
            ];

            $validator = Validator::make($input, $validationRules);

            if ($validator->fails()) {
                return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
            }
            
            $transaksi = Transaksi::find($id);

            if (isset($transaksi)) {
                $transaksi->member_id = $input['member_id'];
                $transaksi->paket_id = $input['paket_id'];
                $transaksi->berat = $input['berat'];
                $transaksi->tgl_mulai = $input['tgl_mulai'];
                $transaksi->tgl_selesai = $input['tgl_selesai'];
                $transaksi->keterangan = $input['keterangan'];
                $transaksi->status_pembayaran = 'belum_lunas';
                $transaksi->status_cucian = 'proses';

                $paket = Paket::find($input['paket_id']);
                $total_harga = $paket->harga * $input['berat'];
                $transaksi->total_harga = $total_harga;


                if(!empty($input['status_pembayaran'])){
                    $transaksi->status_pembayaran = $input['status_pembayaran'];
                }

                if(!empty($input['status_cucian'])){
                    $transaksi->status_cucian = $input['status_cucian'];
                }

                if ($transaksi->save()) {

                    if ($acceptHeader === 'application/json') {
                        $response = [
                            'message' => 'Update Transaksi Success',
                            'status_code' => Response::HTTP_OK,
                            'data' => $transaksi
                        ];
            
                        return response()->json($response, Response::HTTP_OK);
                    } else {
                        $xml = new \SimpleXMLElement('<transaksi/>');
                        $xml->addChild('id', $transaksi->id);
                        $xml->addChild('member_id', $transaksi->member_id);
                        $xml->addChild('berat', $transaksi->berat);
                        $xml->addChild('tgl_mulai', $transaksi->tgl_mulai);
                        $xml->addChild('tgl_selesai', $transaksi->tgl_selesai);
                        $xml->addChild('keterangan', $transaksi->keterangan);
                        $xml->addChild('status_pembayaran', $transaksi->status_pembayaran);
                        $xml->addChild('status_cucian', $transaksi->status_cucian);
                        $xml->addChild('status_pembayaran', $transaksi->status_pembayaran);
                        $xml->addChild('total_harga', $transaksi->total_harga);
                        return $xml->asXML();
                    }
                } else {
                    $response = [
                        'message' => 'Create Transaksi Failed',
                        'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
                    ];
            
                    return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                $response = [
                    'message' => 'Transaksi Not Found',
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
     * Delete Transaksi Function
     */
    public function delete(Request $request, $id)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            $transaksi = Transaksi::where('status_cucian','proses')->find($id);

            if (isset($transaksi)) {
                if ($transaksi->delete()) {

                    if ($acceptHeader === 'application/json') {
                        $response = [
                            'message' => 'Delete Transaksi Success',
                            'status_code' => Response::HTTP_OK
                        ];
            
                        return response()->json($response, Response::HTTP_OK);
                    } else {
                        $xml = new \SimpleXMLElement('<transaksi/>');

                        $xml->addChild('message', 'Delete Transaksi Success');
                        $xml->addChild('status_code', Response::HTTP_OK);

                        return $xml->asXML();
                    }
                } else {
                    $response = [
                        'message' => 'Delete Transaksi Failed',
                        'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
                    ];
        
                    return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                $response = [
                    'message' => 'Transaksi Not Found',
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
