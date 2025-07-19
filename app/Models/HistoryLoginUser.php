<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryLoginUser extends Model
{
    use HasFactory;

    protected $table = 'history_login_user';

    protected $primaryKey = 'kd_history_login';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'kd_history_login',
        'kd_user',
        'tgl_login',
        'bln_login',
        'waktu_login',
        'alamat_device',
        'type_device',
        'device',
    ];
}
