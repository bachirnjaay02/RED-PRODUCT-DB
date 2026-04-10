<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    // On autorise Laravel à remplir ces colonnes
    protected $fillable = [
        'name', 
        'address', 
        'email', 
        'phone', 
        'price', 
        'currency', 
        'image'
    ];
}