<?php

namespace App\Services\Product;

use App\Repositories\Product\ProductRepositoryInterface;

class ProductService {

    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getAll()
    {
        return $this->productRepository->getAll();
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