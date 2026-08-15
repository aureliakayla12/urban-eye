<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'district_id',
        'village_id',
        'title',
        'description',
        'photo',
        'latitude',
        'longitude',
        'address',
        'ai_category',
        'ai_confidence',
        'ai_response',
        'classified_at',
        'verification_status',
        'status',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'ai_confidence' => 'float',
        'classified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function village()
    {
        return $this->belongsTo(Village::class);
    }

    public function images()
    {
        return $this->hasMany(ReportImage::class);
    }

    public function assignments()
    {
        return $this->hasMany(ReportAssignment::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(ReportStatusHistory::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
