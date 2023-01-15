<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;


class BarangUser extends  Model
{
    use HasApiTokens, HasFactory, Notifiable;
    public $table = "barang_user";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    public $timestamps = true;
    public $primaryKey = 'id';
    
    public $fillable = [
        'user_id', 'barang_id',
    ];
}
