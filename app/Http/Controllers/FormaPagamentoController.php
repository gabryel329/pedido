<?php

namespace App\Http\Controllers;

use App\Models\FormaPagamento;
use Illuminate\Http\Request;

class FormaPagamentoController extends RestController
{
    protected $model;
    public function __construct(FormaPagamento $formaPagamento)
    {
        $this->model = $formaPagamento;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->all($this->model);
    }

    public function findById($id)
    {
        return $this->find($this->model,$id);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->create($this->model, $request);
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
        return $this->edit($this->model,$request,$id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return $this->delete($this->model,$id);
    }
}
