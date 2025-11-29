<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterJabatan extends Model
{
    use HasFactory;

    protected $table = 'master_jabatan';
    protected $primaryKey = 'kd_jabatan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kd_jabatan',
        'kd_jabatan_tampil',
        'nama_jabatan',
        'user_input',
        'tgl_input',
        'bln_input',
        'thn_input',
        'waktu_input',
        'alamat_device',
        'type_device',
        'device',
    ];

    public $timestamps = false;
}
