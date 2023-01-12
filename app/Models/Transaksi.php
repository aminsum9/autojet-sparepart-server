<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\DetailTransaksi;
use App\Models\User;

class Transaksi extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    public $table = "transaksi";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'trx_id', 'user_id', 'status', 'subtotal', 'discount', 'grand_total', 'notes',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */

    public function detail_transaksi(){
        return $this->hasMany(DetailTransaksi::class, 'trans_id', 'id');
    }

    public function user(){
        return $this->hasMany(User::class, 'id', 'user_id');
    }
}
