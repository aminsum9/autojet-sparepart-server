<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Barang;

class Supplier extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    public $table = "supplier";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'image', 'address', 'phone', 'desc',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */

     public function barangs()
    {
        return $this->belongsToMany(Barang::class);
    }
}
