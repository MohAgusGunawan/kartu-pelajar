<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    // Tentukan nama tabel (jika tabel bukan plural dari nama model)
    protected $table = 'siswa';

    // Tentukan kolom yang dapat diisi (mass assignable)
    protected $fillable = [
        'nis',
        'nama',
        'ttl',
        'gender',
        'alamat',
        'wa',
        'kelas',
        'foto'
    ];

    // Jika tidak ingin menggunakan kolom created_at dan updated_at
    // public $timestamps = false;
}
