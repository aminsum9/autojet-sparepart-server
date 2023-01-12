<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Supplier;

class Warehouse extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    public $table = "warehouse";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'barang_id', 'user_id', 'supplier_id', 'notes',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */

     public function supplier(){
        return $this->hasOne(Supplier::class, 'id', 'supplier_id');
    }
}
