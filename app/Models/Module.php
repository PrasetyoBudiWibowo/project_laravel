<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $table = 'master_module';
    protected $primaryKey = 'kd_module';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kd_module',
        'nama_module',
        'tampil_module',
        'url_module',
        'status_module',
        'user_input',
        'tgl_input',
        'bln_input',
        'waktu_input',
        'thn_input',
    ];

    public $timestamps = false;
}
