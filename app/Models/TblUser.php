<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblUser extends Model
{
    use HasFactory;

    protected $table = 'tbl_user';

    protected $primaryKey = 'kd_asli_user';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'kd_asli_user',
        'kd_karyawan',
        'nama_user',
        'id_usr_level',
        'password',
        'password_tampil',
        'status_user',
        'blokir',
        'img_user',
        'format_img_user',
        'tgl_input',
        'waktu_input',
        'bln_input',
        'thn_input',
        'device',
        'nama_device',
        'user_input',
    ];

    public function level()
    {
        return $this->belongsTo(LevelUser::class, 'id_usr_level', 'id');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'kd_karyawan', 'kd_karyawan');
    }
}