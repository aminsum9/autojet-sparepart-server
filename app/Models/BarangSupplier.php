<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Barang;

class SupplierBarang extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = "barang_supplier";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    public $timestamps = false;
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'supplier_id', 'barang_id',
    ];
}
