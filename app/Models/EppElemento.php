<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EppElemento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'epp_elementos';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'producto', 'tipo', 'marca', 'certificacion', 
        'observacion', 'stock', 'min_stock', 'talle', 'estado'
    ];
}
