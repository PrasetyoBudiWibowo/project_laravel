<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Negara extends Model
{
    use HasFactory;
    
    protected $table = 'master_negara';
    protected $primaryKey = 'kd_negara';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kd_negara',
        'iso',
        'name',
        'nicename',
        'iso3',
        'numcode',
        'phonecode',
    ];

    public $timestamps = false;
}
