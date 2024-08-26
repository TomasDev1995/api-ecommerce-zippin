<?php

namespace App\Repositories\Product;

use App\Models\Product;
use App\Repositories\Product\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface{
    
    public function getAll()
    {
        return Product::all();
    }

    public function findById(?int $id)
    {
        return Product::find($id);
    }
}