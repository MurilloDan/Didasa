<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AspectoMejora extends Model
{
    protected $table = 'improvement_aspects';

    protected $fillable = [
        'name',
        'icon',
        'is_other',
        'active',
        'sort_order',
    ];

    protected $casts = [
        'is_other' => 'boolean',
        'active' => 'boolean',
    ];

    public function scopeActivos($query)
    {
        return $query->where('active', true);
    }

    public function evaluations(): BelongsToMany
    {
        return $this->belongsToMany(Evaluacion::class, 'evaluation_improvement_aspect', 'improvement_aspect_id', 'evaluation_id')
            ->withPivot('extra_comment')
            ->withTimestamps();
    }
}
