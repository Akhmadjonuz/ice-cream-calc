<?php

namespace App\Http\Controllers;

use App\Http\Requests\Products\CreateProductsRequest;
use App\Http\Requests\Products\GetProductsInputRequest;
use App\Http\Requests\Products\GetProductsPriceLogRequest;
use App\Http\Requests\Products\MakeProductsRequest;
use App\Http\Requests\Products\UpdateProductsRequest;
use App\Models\Expense;
use App\Models\Nbu;
use App\Models\Product;
use App\Models\ProductsInput;
use App\Models\ProductsPriceLog;
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
                $product = Product::with('caterogies')->where('caterogy_id', $caterogy_id)->get();
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
     * Get Product Price Log
     * 
     * @queryParam from_date date nullable 
     * @queryParam to_date date nullable 
     * 
     * @param GetProductsPriceLogRequest $request
     * @return JsonResponse
     */

    public function getProductsPriceLog(GetProductsPriceLogRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $from_date = isset($data['from_date']) ? Carbon::parse($data['from_date'])->startOfDay() : Carbon::now()->startOfDay();
            $to_date = isset($data['to_date']) ? Carbon::parse($data['to_date'])->endOfDay() : Carbon::now()->endOfDay();

            $log = ProductsPriceLog::with('nbu')->whereBetween('created_at', [$from_date, $to_date]);

            $all_price_uzs = $log->sum('price_uzs');
            $all_price_usd = $log->sum('price_usd');

            $success = [
                'all_price_uzs' => round($all_price_uzs, 2),
                'all_price_usd' => round($all_price_usd, 2),
                'data' => $log->get()
            ];

            return $this->success($success, 200);
        } catch (\Exception $e) {
            return $this->log($e);
        }
    }

    /**
     * @group Products
     * 
     * Get Product input
     * 
     * @queryParam from_date date nullable
     * @queryParam to_date date nullable
     * 
     * @param GetProductsInputRequest $request
     * @return JsonResponse
     */

    public function getProductsInput(GetProductsInputRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $from_date = isset($data['from_date']) ? Carbon::parse($data['from_date'])->startOfDay() : Carbon::now()->startOfDay();
            $to_date = isset($data['to_date']) ? Carbon::parse($data['to_date'])->endOfDay() : Carbon::now()->endOfDay();

            $log = ProductsInput::with('nbu')->whereBetween('created_at', [$from_date, $to_date]);

            $all_price_uzs = $log->sum('price_uzs');
            $all_price_usd = $log->sum('price_usd');

            $success = [
                'all_price_uzs' => round($all_price_uzs, 2),
                'all_price_usd' => round($all_price_usd, 2),
                'data' => $log->get()
            ];

            return $this->success($success, 200);
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

            // get nbu
            $usd = Nbu::orderBy('id', 'desc')->first();

            DB::beginTransaction();

            $product = Product::find($data['id']);
            $count = $data['count'] ?? 0;
            $product->caterogy_id = $data['caterogy_id'] ?? $product->caterogy_id;
            $product->name = $data['name'] ?? $product->name;
            $product->price = $data['price'] ?? $product->price;
            $product->count = $product->count + $count;
            $product->type_id = $data['type_id'] ?? $product->type_id;
            $product->nbu_id = $usd->id;

            if (isset($data['is_active']))
                $product->is_active = $product->is_active ? false : true;

            if (!empty($data['price'])) {

                $check = ProductsPriceLog::where('product_id', $product->id)->where('price', $data['price'])->first();

                if (!$check) {
                    $log = new ProductsPriceLog;
                    $log->product_id = $product->id;
                    $log->price = $data['price'];
                    $log->price_uzs = $data['price'] * $usd->nbu_cell_price;
                    $log->price_usd = $data['price'];
                    $log->nbu_id = $usd->id;

                    if ($count > 0)
                        $log->count = $count;
                    else
                        $log->count = 0;

                    $log->save();
                } else {
                    $check->count = $check->count + $count;
                    $check->save();
                }
            } else {
                $check = ProductsPriceLog::where('product_id', $product->id)->where('price', $product->price)->first();
                if ($check) {
                    $check->count = $check->count + $count;
                    $check->save();
                }
            }

            if ($count > 0) {
                $products_input = new ProductsInput;
                $products_input->product_id = $product->id;
                $products_input->quantity = $count;
                $products_input->price_uzs = $product->price * $count;
                $products_input->price_usd = intval($product->price * $count) / $usd->nbu_cell_price;
                $products_input->description = $product->name . ' maxsulotiga ' . $count . ' ta qo\'shildi.';
                $products_input->save();
            }

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
     * export beta
     * 
     */

    public function exportBeta()
    {
        try {
            $all = Product::with('caterogies')->get();

            $nbu = Nbu::orderBy('id', 'desc')->first();

            foreach ($all as $product) {
                $log = new ProductsPriceLog;
                $log->product_id = $product->id;
                $log->price = $product->price;
                if ($product->cyrrency == 0) {
                    $log->price_uzs = $product->price * $product->count;
                    $log->price_usd = intval($product->price * $product->count) / $nbu->nbu_cell_price;
                } elseif ($product->cyrrency == 1) {
                    $log->price_uzs = ($product->price * $product->count) * $nbu->nbu_cell_price;
                    $log->price_usd = $product->price * $product->count;
                }
                $log->nbu_id = $nbu->id;
                $log->count = $product->count;
                $log->save();
            }

            return $this->success('Product added successfully', 200);
        } catch (\Exception $e) {
            return $this->log($e);
        }
    }

    public static function PriceLog(int $product_id, $count, $product_price = 0): bool
    {
        $log = ProductsPriceLog::where('product_id', $product_id)->orderBy('price', 'asc')->all();

        $all_count = 0;

        foreach ($log as $item) {
            $all_count = $all_count + $item->count;
        }

        if ($all_count < $count)
            return false;

        $count = $count;

        foreach ($log as $item) {
            $check = $item->count - $count;

            if ($check == 0 or $check < 0) {
                $item->delete();
                $count = $count - $item->count;
                continue;
            } elseif ($check > 0) {
                $item->count = $check;
                $item->save();
                break;
            }
        }

        return true;
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
            $usd = Nbu::orderBy('id', 'desc')->first()->nbu_cell_price;

            DB::beginTransaction();

            $i = 0;
            $is_checked = 0;
            $spent = [];
            $spent_price_uzs = 0;
            $spent_price_usd = 0;

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

                // call to static function
                $check = $this->PriceLog($is_material->id, $is_value);

                if ($check == false)
                    return $this->error('Homashyo omborda tugabdi nomi: ' . $is_material->name, 404);

                $is_material->count = $is_material->count - $is_value;
                $is_material->save();

                $expense = new Expense;
                $expense->material_id = $is_material->id;

                if ($is_material->cyrrency == 0) {
                    $expense->price_uzs = $is_value * $is_material->price;
                    $expense->price_usd = ($is_value * $is_material->price) / $usd;
                } elseif ($is_material->cyrrency == 1) {
                    $expense->price_uzs = $is_value * ($is_material->price * $usd);
                    $expense->price_usd = $is_value * $is_material->price;
                }

                $spent[] = ['name' => $is_material->name, 'value' => $is_value, 'price_uzs' => $expense->price_uzs, 'price_usd' => $expense->price_usd, 'benefit_uzs' => $product->price - $expense->price_uzs, 'benefit_usd' => $product->price - $expense->price_usd];
                $spent_price_uzs = $spent_price_uzs + $expense->price_uzs;
                $spent_price_usd = $spent_price_usd + $expense->price_usd;

                $expense->type_id = $is_material->type_id;
                $expense->value = $is_value;
                $expense->product_id = $data['product_id'];
                $expense->save();

                $i++;
            }

            $product->count = $product->count + $data['count'];
            $product->save();

            $products_input = new ProductsInput;
            $products_input->product_id = $product->id;
            $products_input->quantity = $data['count'];

            if ($product->cyrrency == 0) {
                $products_input->price_uzs = $product->price * $data['count'];
                $products_input->price_usd = ($product->price * $data['count']) / $usd;
            } elseif ($product->cyrrency == 1) {
                $products_input->price_uzs = ($product->price * $data['count']) * $usd;
                $products_input->price_usd = $product->price * $data['count'];
            }

            $products_input->description = $product->name . ' maxsulotiga ' . $data['count'] . ' ta qo\'shildi.';

            array_push($spent, ['all_price_uzs' => $spent_price_uzs, 'all_price_usd' => $spent_price_usd]);
            $spent = json_encode($spent);

            $products_input->spent = $spent;
            $products_input->save();


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
