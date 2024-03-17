<?php

namespace App\Models;

use App\Models\Properties;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PropertySlots extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'available_units',
        'total_units',
    ];

    public function property()
    {
        return $this->belongsTo(Properties::class, 'property_id');
    }
}
