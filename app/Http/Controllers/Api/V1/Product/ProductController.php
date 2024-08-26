<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Services\Product\ProductService;
use App\Traits\ApiResponse;

/**
 * @OA\Tag(name="Product", description="Product management")
 */
class ProductController extends Controller
{
    use ApiResponse;

    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;        
    }

    /**
     * @OA\Get(
     *     path="/api/v1/products",
     *     summary="Get a list of products",
     *     tags={"Product"},
     *     @OA\Response(
     *         response=200,
     *         description="Returns a list of products",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Product"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No products found"
     *     )
     * )
     */
    public function index()
    {
        $products = $this->productService->getAll();

        if ($products->isEmpty()) {
            return $this->error("No hay productos cargados.", 404);
        }
        return $this->success($products->toArray());
    }

    /**
     * @OA\Post(
     *     path="/api/v1/products",
     *     summary="Create a new product",
     *     tags={"Product"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/Product")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
     */
    public function store(CreateProductRequest $request)
    {
        $product = $this->productService->create($request->validated());
        return $this->success($product, 'Producto creado correctamente', 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/products/{id}",
     *     summary="Get a specific product",
     *     tags={"Product"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the product",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Returns the product details",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function show($id)
    {
        $product = $this->productService->findById($id);
        if (!$product) {
            return $this->error("Producto no encontrado", 404);
        }
        return $this->success($product, 'Producto encontrado');
    }

    /**
     * @OA\Put(
     *     path="/api/v1/products/{id}",
     *     summary="Update a specific product",
     *     tags={"Product"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the product",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/Product")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function update(UpdateProductRequest $request, $id)
    {
        $product = $this->productService->update($id, $request->validated());
        if (!$product) {
            return $this->error("Producto no encontrado", 404);
        }
        return $this->success($product, 'Producto actualizado correctamente');
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/products/{id}",
     *     summary="Delete a specific product",
     *     tags={"Product"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the product",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Producto eliminado exitosamente.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $product = $this->productService->delete($id);
        if (!$product) {
            return $this->error("Producto no encontrado", 404);
        }
        return $this->success(null, 'Producto eliminado exitosamente.');
    }
}
