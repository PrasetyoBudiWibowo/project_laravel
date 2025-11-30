<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryInputKaryawan extends Model
{
    use HasFactory;

    protected $table = 'history_input_master_karyawan';
    protected $primaryKey = 'kd_history_input_master_karyawan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kd_history_input_master_karyawan',
        'jenis_input',
        'keterangan_input',
        'kd_karyawan',
        'nama_karyawan',
        'user_input',
        'note',
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
