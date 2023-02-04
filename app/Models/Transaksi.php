<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';

    /**
     * The attributes that are mass assignable.
     * 
     * @var string[]
     */
    protected $fillable = ['id','member_id','paket_id','berat','tgl_mulai','tgl_selesai','keterangan','status_pembayaran','status_cucian','total_harga'];
    
    protected $hidden = [
    ];

    public function member()
    {
        return $this->hasOne(Member::class,'id','member_id');
    }

    public function paket()
    {
        return $this->hasOne(Paket::class,'id','paket_id');
    }

}
