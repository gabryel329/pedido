<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pedido extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pedido';
    protected $fillable = ['total', 'nome', 'telefone', 'forma_pagamento_id', 'status_id'];
    protected $dates = ['deleted_at'];

    public function produtos()
    {
        return $this->belongsToMany(Produto::class, 'pedido_produto', 'pedido_id', 'produto_id')
                    ->withPivot('quantidade')
                    ->withTimestamps();
    }
    public function formaPagamento()
    {
        return $this->belongsTo(FormaPagamento::class, 'forma_pagamento_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }
}
