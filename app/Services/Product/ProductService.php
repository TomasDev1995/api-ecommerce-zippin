<?php

namespace App\Services\Product;

use App\Repositories\Product\ProductRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class ProductService {

    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getAll()
    {
        // Consultamos primero la caché
        return Cache::remember('products.all', 60, function () {
            // Delegamos la obtención de productos al repositorio si es que no encontramos en la caché.
            return $this->productRepository->getAll();
        });
    }

    public function findById(?int $id)
    {
        return $this->productRepository->findById($id);
    }

    public function create(array $data)
    {
        return $this->productRepository->create($data);
    }

    public function update()
    {
        //
    }

    public function delete()
    {
        //
    }
}