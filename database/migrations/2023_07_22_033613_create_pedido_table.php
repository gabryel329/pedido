<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedidoTable extends Migration
{
    public function up()
    {
        Schema::create('pedido', function (Blueprint $table) {
            $table->id();
            $table->string('total', 250)->nullable();
            $table->string('nome', 150);
            $table->string('telefone', 25);
            $table->integer('status_id');
            $table->foreign('status_id')->references('id')->on('status')->onDelete('cascade');
            $table->integer('forma_pagamento_id');
            $table->foreign('forma_pagamento_id') ->references('id')->on('forma_pagamento')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pedido');
    }
}
