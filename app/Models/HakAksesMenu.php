<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HakAksesMenu extends Model
{
    use HasFactory;

    protected $table = 'hak_akses_menu';
    protected $primaryKey = 'kd_hak_akses_menu';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kd_hak_akses_menu',
        'kd_user',
        'kd_menu',
        'bisa_insert',
        'bisa_edit',
        'bisa_export',
        'status_akses',
        'tgl_input',
        'bln_input',
        'thn_input',
        'user_input',
        'waktu_input',
        'device',
        'alamat_device',
        'type_device',
    ];

    public $timestamps = false;

    public function menu()
    {
        return $this->belongsTo(MasterMenu::class, 'kd_menu', 'kd_menu');
    }
}