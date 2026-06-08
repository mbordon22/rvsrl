<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notificacion extends Model
{
    use SoftDeletes;

    protected $table = 'notificaciones';

    protected $fillable = [
        'notificable_type',
        'notificable_id',
        'user_id',
        'tipo',
        'titulo',
        'mensaje',
        'url',
        'fecha_programada',
        'leida_at',
    ];

    protected $casts = [
        'fecha_programada' => 'date',
        'leida_at' => 'datetime',
    ];

    public function notificable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeNoLeidas($query)
    {
        return $query->whereNull('leida_at');
    }
}
