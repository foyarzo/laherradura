<?php

namespace App\Http\Controllers;

use App\Models\Product;

class TiendaController extends Controller
{
    public function index()
    {
        $products = Product::orderByDesc('id')->get();

        return view('tienda.home', [
            'products' => $products,
        ]);
    }
}