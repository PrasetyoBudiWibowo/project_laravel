<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterMenu extends Model
{
    use HasFactory;

    protected $table = 'master_menu';
    protected $primaryKey = 'kd_menu';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kd_menu',
        'kd_module',
        'nama_menu',
        'urutan',
        'url_menu',
        'parent_menu',
        'icon_menu',
        'status_akses',
        'user_input',
        'tgl_input',
        'bln_input',
        'thn_input',
        'waktu_input',
        'device',
        'alamat_device',
        'type_device',
    ];

    public $timestamps = false;

    public function module()
    {
        return $this->belongsTo(Module::class, 'kd_module', 'kd_module');
    }

    public function parent()
    {
        return $this->belongsTo(MasterMenu::class, 'parent_menu', 'kd_menu');
    }

    public function children()
    {
        return $this->hasMany(MasterMenu::class, 'parent_menu', 'kd_menu');
    }
}
