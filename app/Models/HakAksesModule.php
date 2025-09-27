<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HakAksesModule extends Model
{
    use HasFactory;

    protected $table = 'hak_akses_module';
    protected $primaryKey = 'kd_hak_akses_module';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kd_hak_akses_module',
        'kd_user',
        'kd_module',
        'status_akses',
        'user_input',
        'tgl_input',
        'bln_input',
        'thn_input',
        'waktu_input',
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(TblUser::class, 'kd_user', 'kd_asli_user');
    }

    public function module()
    {
        return $this->belongsTo(Module::class, 'kd_module', 'kd_module');
    }
}
