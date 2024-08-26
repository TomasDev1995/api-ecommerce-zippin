<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Services\Product\ProductService;
use App\Traits\ApiResponse;

class ProductController extends Controller
{
    use ApiResponse;

    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;        
    }

    public function index()
    {
        $products = $this->productService->getAll();

        if (empty($products)) {
            return $this->error("No hay productos cargados.", 404);
        }
        
        return $this->success($products);
    }

    public function store(CreateProductRequest $request)
    {
        $product = $this->productService->create($request->validated());

        if (!$product) {
            return $this->error("No se pudo crear el producto.", 500);
        }

        return $this->success($product, "Producto creado exitosamente.", 201);
    }

    public function show(int $id)
    {
        $product = $this->productService->findById($id);

        if (!$product) {
            return $this->error("Producto no encontrado.", 404);
        }

        return $this->success($product);
    }

    public function update(UpdateProductRequest $request, int $id)
    {
        $validatedData = $request->validated();
        $product = $this->productService->update($id, $validatedData);

        if (!$product) {
            return $this->error("No se pudo actualizar el producto.", 500);
        }

        return $this->success($product, "Producto actualizado exitosamente.");
    }

    public function destroy(int $id)
    {
        $result = $this->productService->delete($id);

        if (!$result) {
            return $this->error("No se pudo eliminar el producto.", 500);
        }

        return $this->success(null, "Producto eliminado exitosamente.");
    }
}
