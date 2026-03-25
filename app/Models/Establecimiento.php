<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Establecimiento extends Model
{
    //
    protected $table = 'establecimiento';
    protected $fillable = [
        'nombre',
        'direccion',
        'descripcion',
        'imagen',
        'telefono',
        'email',
        'horario_apertura',
        'horario_cierre',
        'categoria_id',
        'latitud',
        'longitud',
        'estado',
    ];

    protected $hidden = ['user_id'];
    protected $casts = [
        'horario_apertura' => 'datetime:H:i',
        'horario_cierre' => 'datetime:H:i',
        'latitud' => 'float',
        'longitud' => 'float',
    ];

    protected $appends = ['imagen_url'];

    protected function getImagenUrlAttribute(): string
    {
        return $this->imagen
            ? asset('storage/' . $this->imagen)
            : asset('images/default-placeholder.png');
    }

    // obtener el id de usuario que creó el establecimiento del jwt
    public static function boot()
    {
        parent::boot();

        static::creating(function ($establecimiento) {
            $establecimiento->user_id = auth('api')->user()->id;
        });
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function servicios()
    {
        return $this->hasMany(Servicio::class, 'establecimiento_id');
    }
}
