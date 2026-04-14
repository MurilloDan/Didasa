<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evaluacion extends Model
{
    protected $table = 'evaluations';

    const GOOD = 'good';    // 😊
    const FAIR = 'fair';    // 😐
    const POOR = 'poor';    // 😞

    const RATINGS = [
        self::GOOD,
        self::FAIR,
        self::POOR,
    ];

    const SCORE = [
        self::GOOD => 100,
        self::FAIR => 50,
        self::POOR => 0,
    ];

    protected $fillable = [
        'employee_id',
        'rating',
        'comment',
        'client_ip',
        'device',
    ];

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class, 'employee_id');
    }

    public function getEmojiAttribute(): string
    {
        return match ($this->rating) {
            self::GOOD  => '😊',
            self::FAIR  => '😐',
            self::POOR  => '😞',
            default     => '❓',
        };
    }
}
