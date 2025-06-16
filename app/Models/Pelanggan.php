<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    protected $fillable = [
        'id_pelanggan',
        'nama',
        'alamat',
        'no_hp',
        'email',
        'password',
        'id_paket',
        'remark1',
        'remark2',
        'remark3',
        'id_server',
        'ip_router',
        'ip_parent_router',
    ];

    public function paket()
    {
        return $this->belongsTo(Paket::class, 'id_paket', 'id');
    }

    public function server()
    {
        return $this->belongsTo(Server::class, 'id_server',);
    }
}
