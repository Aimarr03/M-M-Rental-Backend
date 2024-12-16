<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranModel extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'pembayaran';
    protected $fillable = [
        'reservasi_id',
        'tanggal_pembayaran',
        'metode_pembayaran_id',
        'jumlah',
        'status',
    ];

    public function reservasi()
    {
        return $this->belongsTo(ReservasiModel::class, 'reservasi_id');
    }

    public function metode()
    {
        return $this->belongsTo(MetodePembayaranModel::class, 'metode_pembayaran_id');
    }
}
