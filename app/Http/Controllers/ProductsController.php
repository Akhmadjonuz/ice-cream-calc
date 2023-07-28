<?php

namespace App\Http\Controllers;

use App\Http\Requests\Products\CreateProductsRequest;
use App\Http\Requests\Products\UpdateProductsRequest;
use App\Models\Product;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductsController extends Controller
{
    use HttpResponses;

    /**
     * @group Products
     * 
     * Get all products
     * 
     * @param int $id
     * @return JsonResponse
     */

    public function getProducts(Request $request, int $id): JsonResponse
    {
        try {
            if ($id)
                $product = Product::find($id);
            else
                $product = Product::all();

            return $this->success($product);

        } catch (\Exception $e) {
            return $this->log($e);
        }
    }

    /**
     * @group Products
     * 
     * Create new product
     * 
     * @param CreateProductsRequest $request
     * @return JsonResponse
     */

    public function createProduct(CreateProductsRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            DB::beginTransaction();

            $product = new Product();
            $product->caterogy_id = $data['caterogy_id'];
            $product->name = $data['name'];
            $product->price = $data['price'];
            $product->type_id = $data['type_id'];
            $product->cyrrency = $data['cyrrency'];
            $product->save();

            DB::commit();

            return $this->success('Product created successfully', 200);
        } catch (\Exception $e) {
            return $this->log($e);
        }
    }

    /**
     * @group Products
     * 
     * Update product
     * 
     * @param UpdateProductsRequest $request
     * @return JsonResponse
     */

    public function updateProduct(UpdateProductsRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            DB::beginTransaction();

            $product = Product::find($data['id']);
            $product->caterogy_id = $data['caterogy_id'];
            $product->name = $data['name'];
            $product->price = $data['price'];
            $product->type_id = $data['type_id'];

            if (isset($data['is_active']))
                $product->is_active = $product->is_active ? false : true;

            $product->save();

            DB::commit();

            return $this->success('Product updated successfully', 200);
        } catch (\Exception $e) {
            return $this->log($e);
        }
    }
}