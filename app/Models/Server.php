<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Server extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip',
        'user',
        'password',
        'lokasi',
        'no_int',
        'mikrotik',
        'remark1',
        'remark2',
        'remark3',
    ];
}
