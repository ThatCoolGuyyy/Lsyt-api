<?php

namespace App\Models;

use App\Models\Users;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tenants extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'property_id',
        'status',
        'has_paid'
    ];

    public function user()
    {
        return $this->belongsTo(Users::class);
    }

    public function property()
    {
        return $this->belongsTo(Properties::class);
    }

    public function complaints()
    {
        return $this->hasMany(Complaints::class, 'tenant_id');
    }
}
