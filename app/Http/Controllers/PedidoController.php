<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    protected $model;
    public function __construct(Pedido $pedido)
    {
        $this->model = $pedido;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pedidos = Pedido::with(['produtos', 'formaPagamento', 'status'])->get();
        return response()->json($pedidos);
    }

    public function findById($id)
    {
        $pedidos = Pedido::with(['produtos', 'formaPagamento', 'status'])->find($id);
        return response()->json($pedidos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $pedido = $this->model::create($data);

        foreach ($request->produtos as $produto) {
            $pedido->produtos()->attach($produto['id'], ['quantidade' => $produto['quantidade']]);
        }

        return response()->json($pedido, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        $pedido = Pedido::find($id);

        if (!$pedido) {
            return response()->json(['message' => 'Pedido não encontrado'], 404);
        }

        $pedido->update([
            'total' => $request->input('total'),
            'nome' => $request->input('nome'),
            'telefone' => $request->input('telefone'),
            'forma_pagamento_id' => $request->input('forma_pagamento_id'),
            'status_id' => $request->input('status_id'),
        ]);

        $pedido->produtos()->detach();

        foreach ($request->input('produtos') as $produto) {
            $pedido->produtos()->attach($produto['id'], ['quantidade' => $produto['quantidade']]);
        }

        return response()->json($pedido, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return $this->delete($this->model,$id);
    }
}
