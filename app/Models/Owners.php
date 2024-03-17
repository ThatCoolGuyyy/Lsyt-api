<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Owners extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function properties()
    {
        return $this->hasMany(Properties::class, 'owner_id');
    }

    // public function complaints()
    // {
    //     return $this->hasMany(Complaints::class, 'owner_id');
    // }

    // public function payments()
    // {
    //     return $this->hasMany(Payments::class, 'owner_id');
    // }
}
