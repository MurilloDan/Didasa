<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    protected $table = 'departments';

    protected $fillable = ['name', 'description', 'active'];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function empleados(): HasMany
    {
        return $this->hasMany(Empleado::class, 'department_id');
    }

    public function empleadosActivos(): HasMany
    {
        return $this->hasMany(Empleado::class, 'department_id')->where('active', true);
    }
}
