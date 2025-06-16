<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paket extends Model
{
    protected $fillable = [
        'id_paket',
        'nama',
        'harga',
        'kecepatan',
        'durasi',
        'remark1',
        'remark2',
        'remark3',
    ];


    /**
     * Relasi: satu paket memiliki banyak pelanggan
     */
    public function pelanggans(): HasMany
    {
        return $this->hasMany(Pelanggan::class, 'id_paket', 'id');
    }
}
