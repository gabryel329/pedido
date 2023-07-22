<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'produto';
    protected $fillable = ['nome', 'preco', 'descricao'];
    protected $dates = ['deleted_at'];

    public function pedidos()
    {
        return $this->belongsToMany(Pedido::class, 'pedido_produto', 'produto_id', 'pedido_id')
                    ->withPivot('quantidade')
                    ->withTimestamps();
    }
}
