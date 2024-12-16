<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'car';
    protected $fillable = [
        'nama_mobil',
        'tahun',
        'plat_nomor',
        'id_jenis',
        'kapasitas_penumpang',
        'harga_sewa',
        'foto',
        'status_id',
        'transmisi',
    ];

    public function jenis()
    {
        return $this->belongsTo(JenisMobilModel::class, 'id_jenis');
    }

    public function status()
    {
        return $this->belongsTo(StatusModel::class, 'status_id');
    }
}
