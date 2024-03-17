<?php

namespace App\Models;

use App\Models\User;
use App\Models\PropertySlots;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Properties extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'owner_id',
        'title',
        'description',
        'address',
        'city',
        'state',
        'country',
        'price',
        'image_url',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function slot()
    {
        return $this->hasOne(PropertySlots::class, 'property_id');
    }

    public function tenants()
    {
        return $this->hasMany(Tenants::class, 'property_id');
    }
}
