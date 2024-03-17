<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Complaints extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'property_id',
        'title',
        'description',
        'status'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenants::class);
    }
    
}
