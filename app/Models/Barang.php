<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Warehouse;
use App\Models\Supplier;

class Barang extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    public $table = "barang";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'alias', 'image', 'qty', 'price', 'discount', 'desc',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */

    public function warehouses(){
        return $this->hasMany(Warehouse::class, 'barang_id', 'id');
    }

    public function suppliers(){
        return $this->belongsToMany(Supplier::class);
    }
}
