<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paket extends Model
{
    protected $table = 'paket';

    /**
     * The attributes that are mass assignable.
     * 
     * @var string[]
     */
    protected $fillable = ['id','nama','lama_pengerjaan','jenis_pengerjaan','harga'];
    
    protected $hidden = [
    ];

}
