<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $table = 'member';

    /**
     * The attributes that are mass assignable.
     * 
     * @var string[]
     */
    protected $fillable = ['id','pengguna_id','nama','alamat','no_hp'];
    
    protected $hidden = [
    ];

}
