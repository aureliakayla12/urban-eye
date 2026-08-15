<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'required_points',
        'required_reports',
        'icon',
    ];

    public function userBadges()
    {
        return $this->hasMany(UserBadge::class);
    }
}
