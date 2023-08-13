<?php

namespace App\Http\Controllers;

use App\Http\Requests\Products\CreateProductsRequest;
use App\Http\Requests\Products\MakeProductsRequest;
use App\Http\Requests\Products\UpdateProductsRequest;
use App\Models\Expense;
use App\Models\Nbu;
use App\Models\Product;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
                $product = Product::with('caterogies')->find($caterogy_id);
            else
                $product = Product::with('caterogies')->get();

            return $this->success($product, 200);

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
     * @bodyParam price string required The price of the product. Example: 100
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
     * @bodyParam price string nullable The price of the product. Example: 100
     * @bodyParam count string nullable The count of the product. Example: 100
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
     * @bodyParam count string required The count of the product. Example: 100
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
            $materials = $data['materials'];
            $original_values = $data['values'];

            DB::beginTransaction();

            $i = 0;
            $is_checked = 0;
            foreach ($original_values as $is_value) {
                
                if (empty($is_value) and $i == 0) {
                    return $this->error('Maxsulot uchun miqdorni kiritmadingiz !', 404);
                } elseif (empty($is_value)) {
                    $i++;
                    continue;
                } elseif ($i == 0 and $is_checked == 0) {
                    $is_checked = 1;
                    continue;
                }

                $is_material = Product::where('id', $materials[$i])->first();
                if (!$is_material)
                    return $this->error('Homashyo topilmadi nomi: ' . $is_material->name, 404);

                // if count of material less than value
                if ($is_material->count < $is_value)
                    return $this->error('Homashyo omborda tugabdi nomi: ' . $is_material->name . ' Omborda bor qolgan: ' . $is_material->count, 404);

                $is_material->count = $is_material->count - $is_value;
                $is_material->save();

                $expense = new Expense();
                $expense->material_id = $is_material->id;

                $usd = Nbu::orderBy('id', 'desc')->first()->nbu_cell_price;

                if ($is_material->cyrrency == 0) {
                    $expense->price_uzs = $is_value * $is_material->price;
                    $expense->price_usd = ($is_value * $is_material->price) / $usd;
                } elseif ($is_material->cyrrency == 1) {
                    $expense->price_uzs = $is_value * ($is_material->price * $usd);
                    $expense->price_usd = $is_value * $is_material->price;
                }

                $expense->type_id = $is_material->type_id;
                $expense->value = $is_value;
                $expense->product_id = $data['product_id'];
                $expense->save();

                $i++;
            }

            $product->count = $product->count + $data['count'];
            $product->save();

            $exp = new Expense();
            $exp->material_id = 1;
            $exp->type_id = 1;
            $exp->count = $data['count'];
            $exp->product_id = $data['product_id'];
            $exp->save();

            DB::commit();

            return $this->success('Hammasi muvaffaqiyatli bo\'ldi!', 200);
        } catch (\Exception $e) {
            return $this->log($e);
        }
    }
}