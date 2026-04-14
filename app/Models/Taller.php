<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Taller extends Model
{
    use HasFactory;

    protected $table = 'workshops';

    protected $fillable = ['name', 'city', 'active'];

    protected $casts = ['active' => 'boolean'];

    public function scopeActivos($query)
    {
        return $query->where('active', true);
    }

    public function empleados(): HasMany
    {
        return $this->hasMany(Empleado::class, 'workshop_id');
    }
}
