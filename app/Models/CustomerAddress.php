<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','first_name','last_name','email','mobile','country_id','address','apartment','state','city','zip'];
}
