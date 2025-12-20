<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class agama extends Model
{
    use HasFactory;

    protected $table = 'master_agama';
    protected $primaryKey = 'kd_agama';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kd_agama',
        'nama_agama',
        'status',
        'tgl_input',
        'waktu_input',
        'user_input',
    ];

    public $timestamps = false;
}
