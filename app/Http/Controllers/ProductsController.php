<?php

namespace App\Http\Controllers;

use App\Http\Requests\Products\CreateProductsRequest;
use App\Http\Requests\Products\MakeProductsRequest;
use App\Http\Requests\Products\UpdateProductsRequest;
use App\Models\Nbu;
use App\Models\Product;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ProductsController extends Controller
{
    use HttpResponses;

    /**
     * @group Products
     * 
     * Get all products
     * 
     * @param int $caterogy_id
     * @return JsonResponse
     */

    public function getProducts($caterogy_id = null): JsonResponse
    {
        try {
            if ($caterogy_id)
                $product = Product::find($caterogy_id);
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
     * @bodyParam caterogy_id integer required The id of the caterogy. Example: 1
     * @bodyParam name string required The name of the product. Example: Product 1
     * @bodyParam price integer float The price of the product. Example: 100
     * @bodyParam type_id integer required The id of the type. Example: 1
     * @bodyParam cyrrency boolean required The cyrrency of the product. Example: 0 or 1
     * @bodyParam type boolean nullable The type of the product. Example: 0 or 1
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

            // select last from nbu table
            $usd = Nbu::orderBy('id', 'desc')->first();

            // if ($data['cyrrency'] == true)
            //     $product->price = $data['price'] * $usd->nbu_cell_price;
            // else
            //     $product->price = $data['price'];

            $product->price = $data['price'];
            $product->nbu_id = $usd->id;
            $product->type = $data['type'];
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
     * @bodyParam id integer required The id of the product. Example: 1
     * @bodyParam caterogy_id integer nullable The id of the caterogy. Example: 1
     * @bodyParam name string nullable The name of the product. Example: Product 1
     * @bodyParam price float nullable The price of the product. Example: 100
     * @bodyParam count float nullable The count of the product. Example: 100
     * @bodyParam type_id integer nullable The id of the type. Example: 1
     * @bodyParam is_active boolean nullable The status of the product. Example: true
     *  
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
            $count = $data['count'] ?? 0;
            $product->caterogy_id = $data['caterogy_id'] ?? $product->caterogy_id;
            $product->name = $data['name'] ?? $product->name;
            $product->price = $data['price'] ?? $product->price;
            $product->count = $product->count + $count;
            $product->type_id = $data['type_id'] ?? $product->type_id;

            if (isset($data['is_active']))
                $product->is_active = $product->is_active ? false : true;

            $product->save();

            DB::commit();

            return $this->success('Product updated successfully', 200);
        } catch (\Exception $e) {
            return $this->log($e);
        }
    }


    /**
     * @group Products
     * 
     * Make product
     * 
     * @bodyParam product_id integer required The id of the product. Example: 1
     * @bodyParam count float required The count of the product. Example: 100
     * @bodyParam materials string required The materials of the product. Example: 1,2,3
     * @bodyParam values string required The values of the product. Example: 1,2,3
     * 
     * @param MakeProductsRequest $request
     * @return JsonResponse
     */

    public function MakeProduct(MakeProductsRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $product = Product::find($data['product_id']);

            // parse materials
            $materials = explode(',', $data['materials']);
            $values = explode(',', $data['values']);

            DB::beginTransaction();

            $i = 0;
            foreach ($materials as $key => $material) {

                // set break
                if ($i == (count($materials) - 1))
                    break;

                // if not exist material
                $check = Product::where('id', $material)->first();
                if (!$check)
                    return $this->error('Material not found. ID: ' . $material, 404);

                // if product type another material type
                if ($check->type == false)
                    return $this->error('Product type another material type. ID: ' . $material, 404);

                // if value null or empty
                if (empty($values[$i]))
                    return $this->error('Value not found. ID: ' . $material, 404);

                // if count of material less than value
                if ($check->count < $values[$i])
                    return $this->error('Count of material less than value. ID: ' . $material, 404);

                $check->count = $check->count - $values[$i];
                $check->save();

                $i++;
            }

            $product->count = $product->count + $data['count'];
            $product->save();

            DB::commit();

            return $this->success('Added count of product successfully', 200);
        } catch (\Exception $e) {
            return $this->log($e);
        }
    }
}