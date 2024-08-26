<?php

namespace App\Repositories\Product;

use App\Models\Product;
use App\Repositories\Product\ProductRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class ProductRepository implements ProductRepositoryInterface{
    
    public function getAll()
    {
        return Product::all();
    }

    public function findById(?int $id)
    {
        return Product::find($id);
    }

    public function create(array $data)
    {
        $product = Product::create($data);
        Cache::forget('products.all');
        return $product;
    }
}