<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'master_karyawan';
    protected $primaryKey = 'kd_karyawan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kd_karyawan',
        'nama_karyawan',
        'nama_panggilan_karyawan',
        'gender',
        'tgl_lahir',
        'bln_lahir',
        'thn_lahir',
        'email_pribadi',
        'kd_agama',
        'kd_negara',
        'no_ktp',
        'npwp',
        'tgl_awal_kontrak',
        'tgl_bergabung',
        'bln_bergabung',
        'thn_bergabung',
        'tgl_akhir_kontrak',
        'status_kontrak',
        'tgl_keluar',
        'bln_keluar',
        'thn_keluar',
        'foto_karyawan',
        'format_gambar',
        'gaji_angka',
        'gaji_terbilang',
        'kd_provinsi_lahir',
        'kd_kota_kab_lahir',
        'kd_kecamatan_lahir',
        'alamat_lahir',
        'kd_provinsi_tinggal',
        'kd_kota_kab_tinggal',
        'kd_kecamatan_tinggal',
        'alamat_tinggal',
        'tinggi_karyawan',
        'berat_karyawan',
        'kd_divisi',
        'kd_departement',
        'kd_jabatan',
        'kd_position',
        'status_karyawan',
        'daftar_sistem',
        'no_telp1',
        'no_telp2',
        'no_telp3',
        'user_input',
        'tgl_input',
        'bln_input',
        'thn_input',
        'waktu_input',
        'alamat_device',
        'type_device',
        'device',
        'user_ubah',
        'tgl_ubah',
        'bln_ubah',
        'thn_ubah',
        'waktu_ubah',
        'alamat_device_ubah',
        'type_device_ubah',
        'device_ubah',
        'daftar_sales',
        'daftar_spv_sales',
    ];

    public $timestamps = false;

    public function HistoryKontrak()
    {
        return $this->hasMany(HistoryKontrakKaryawan::class, 'kd_karyawan', 'kd_karyawan');
    }

    public function HistoryPenempatan()
    {
        return $this->hasMany(HistoryPenempatanKaryawan::class, 'kd_karyawan', 'kd_karyawan');
    }

    public function Divisi()
    {
        return $this->belongsTo(Divisi::class, 'kd_divisi', 'kd_divisi');
    }

    public function Departement()
    {
        return $this->belongsTo(Departement::class, 'kd_departement', 'kd_departement');
    }

    public function Posisi()
    {
        return $this->belongsTo(Posisi::class, 'kd_position', 'kd_position');
    }

    public function JabatanKaryawan()
    {
        return $this->belongsTo(MasterJabatan::class, 'kd_jabatan', 'kd_jabatan');
    }

    public function ProvinsiLahir()
    {
        return $this->belongsTo(Provinsi::class, 'kd_provinsi_lahir', 'kd_provinsi');
    }

    public function ProvinsiTinggal()
    {
        return $this->belongsTo(Provinsi::class, 'kd_provinsi_tinggal', 'kd_provinsi');
    }

    public function KotaKabLahir()
    {
        return $this->belongsTo(KotaKabupaten::class, 'kd_kota_kab_lahir', 'kd_kota_kabupaten');
    }

    public function KotaKabTinggal()
    {
        return $this->belongsTo(KotaKabupaten::class, 'kd_kota_kab_tinggal', 'kd_kota_kabupaten');
    }

    public function KecamatanLahir()
    {
        return $this->belongsTo(Kecamatan::class, 'kd_kecamatan_lahir', 'kd_kecamatan');
    }

    public function KecamatanTinggal()
    {
        return $this->belongsTo(Kecamatan::class, 'kd_kecamatan_tinggal', 'kd_kecamatan');
    }

    public function Negara()
    {
        return $this->belongsTo(Negara::class, 'kd_negara', 'kd_negara');
    }

    public function Agama()
    {
        return $this->belongsTo(agama::class, 'kd_agama', 'kd_agama');
    }
}
