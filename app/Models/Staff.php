<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staff';

    protected $fillable = ['name', 'role', 'initials', 'avatar_bg', 'active'];

    protected $casts = ['active' => 'boolean'];
}
