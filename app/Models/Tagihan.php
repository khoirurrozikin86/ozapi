<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    protected $fillable = [
        'no_tagihan',
        'id_bulan',
        'tahun',
        'id_pelanggan',
        'jumlah_tagihan',
        'status',
        'tgl_bayar',
        'user_id',
        'remark1',
        'remark2',
        'remark3',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
