<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CuadrillaUser extends Model
{
    use SoftDeletes;

    protected $table = 'cuadrillas_users';

    protected $fillable = [
        'cuadrilla_id',
        'user_id',
        'oficial',
        'role'
    ];

    protected $casts = [
        'cuadrilla_id' => 'integer',
        'user_id' => 'integer'
    ];

    public function cuadrilla()
    {
        return $this->belongsTo(Cuadrilla::class, 'cuadrilla_id');
    }
}
