<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empleado extends Model
{
    protected $table = 'employees';

    protected $fillable = [
        'workshop_id',
        'department_id',
        'first_name',
        'last_name',
        'position',
        'photo',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    protected $appends = ['full_name'];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'department_id');
    }

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Taller::class, 'workshop_id');
    }

    public function evaluaciones(): HasMany
    {
        return $this->hasMany(Evaluacion::class, 'employee_id');
    }

    public function reportesQuincenales(): HasMany
    {
        return $this->hasMany(ReporteQuincenal::class, 'employee_id');
    }
}
