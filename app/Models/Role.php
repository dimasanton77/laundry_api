<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'role';

    /**
     * The attributes that are mass assignable.
     * 
     * @var string[]
     */
    protected $fillable = ['nama'];
    
    protected $hidden = [
    ];

}
