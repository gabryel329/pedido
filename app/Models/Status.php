<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Status extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'status';
    protected $fillable = ['nome'];
    protected $dates = ['deleted_at'];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'status_id');
    }
}
