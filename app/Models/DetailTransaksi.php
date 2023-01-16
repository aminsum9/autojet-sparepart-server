<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Transaksi;
use App\Models\User;
use App\Models\Barang;

class DetailTransaksi extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    public $table = "detail_transaksi";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    public $fillable = [
        'trans_id', 'barang_id', 'qty', 'subtotal', 'discount', 'grand_total', 'notes',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    public function transaksi(){
        return $this->belongsTo(Transaksi::class, 'id', 'trans_id');
    }

    public function barang(){
        return $this->hasOne(Barang::class, 'id', 'barang_id');
    }
}
