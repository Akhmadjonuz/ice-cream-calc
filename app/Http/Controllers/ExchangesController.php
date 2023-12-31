<?php

namespace App\Http\Controllers;

use App\Exports\ExportExchanges;
use App\Http\Requests\CreateExchangesRequest;
use App\Http\Requests\DeleteExchangesRequest;
use App\Http\Requests\EditExchangesRequest;
use App\Http\Requests\GetExchangesRequest;
use App\Models\Exchange;
use App\Models\Nbu;
use App\Models\ProductsPriceLog;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ExchangesController extends Controller
{
    use HttpResponses;

    /**
     * @group Exchanges
     * 
     * get all exchanges
     * 
     * @bodyParam product_id integer nullable
     * @bodyParam partner_id integer nullable  
     * @bodyParam caterogy_id integer nullable  
     * @bodyParam type_id integer nullable 
     * @bodyParam cyrrency boolean nullable 
     * @bodyParam from_date date nullable 
     * @bodyParam to_date date nullable 
     * 
     * @param GetExchangesRequest $request
     * @return JsonResponse
     */

    public function getExchanges(GetExchangesRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $from_date = isset($data['from_date']) ? Carbon::parse($data['from_date'])->startOfDay() : Carbon::now()->startOfDay();
            $to_date = isset($data['to_date']) ? Carbon::parse($data['to_date'])->endOfDay() : Carbon::now()->endOfDay();

            // create query builder
            $query = Exchange::query()->with(['products', 'partners', 'products.caterogies', 'products.nbu', 'products.settings'])->orderBy('id', 'desc');

            // filter by product
            if (isset($data['product_id']) and $data['product_id'] != 0)
                $query->where('product_id', $data['product_id']);

            // filter by partner
            if (isset($data['partner_id']) and $data['partner_id'] != 0)
                $query->where('partner_id', $data['partner_id']);

            // filter by cyrrency type (UZS or USD) get data from hasMany relation
            if (isset($data['cyrrency']) and $data['cyrrency'] != 0)
                $query->whereHas('products', function ($q) use ($data) {
                    $q->where('cyrrency', $data['cyrrency']);
                });

            // filter by caterogy get data from hasMany relation
            if (isset($data['caterogy_id']) and $data['caterogy_id'] != 0)
                $query->whereHas('products', function ($q) use ($data) {
                    $q->where('caterogy_id', $data['caterogy_id']);
                });

            // filter by type get data from hasMany relation
            if (isset($data['type_id']) and $data['type_id'] != 0)
                $query->whereHas('products', function ($q) use ($data) {
                    $q->where('type_id', $data['type_id']);
                });

            // filter by from date and to date
            $query->whereBetween('created_at', [$from_date, $to_date])->orderBy('id', 'desc');

            $success = [
                'all_count' => $query->sum('value'),
                'price_uzs_all' => round($query->sum('price_uzs'), 2),
                'price_usd_all' => round($query->sum('price_usd'), 2),
                'data' => $query->get()
            ];

            // Return success response
            return $this->success($success, 200);
        } catch (\Exception $e) {
            return $this->log($e);
        }
    }

    /**
     * @group Exchanges
     * 
     * create exchange
     * 
     * @bodyParam product_id integer required The id of the product. Example: 1
     * @bodyParam partner_id integer required The id of the partner. Example: 1
     * @bodyParam value integer required The value of the exchange. Example: 1000
     * 
     * @response {
     * "result": "Exchange created successfully",
     * }
     * 
     * 
     * @param CreateExchangesRequest $request
     * @return JsonResponse
     */

    public function create(CreateExchangesRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            DB::beginTransaction();

            $exchange = new Exchange();
            $exchange->product_id = $data['product_id'];
            $exchange->partner_id = $data['partner_id'];
            $exchange->value = $data['value'];

            // product
            $product = $exchange->products->first();

            // save to storage->laravel.log file $product
            // Log::info($exchange->products);

            $usd = Nbu::orderBy('id', 'desc')->first()->nbu_cell_price;

            if ($product->cyrrency == 0) {
                $exchange->price_uzs = $data['value'] * $product->price;
                $exchange->price_usd = round(($data['value'] * $product->price) / $usd, 2);
            } elseif ($product->cyrrency == 1) {
                $exchange->price_uzs = $data['value'] * ($product->price * $usd);
                $exchange->price_usd = $data['value'] * $product->price;
            }

            // update product quantity
            $result = $product->count - $data['value'];
            $product->nbu_id = Nbu::orderBy('id', 'desc')->first()->id;


            // call to static function from ProductsController
            $check = ProductsController::PriceLog($product->id, $data['value']);

            if ($check == false)
                return $this->error('Insufficient stock!', 400);

            $product->count = $result;
            
            // $log = ProductsPriceLog::where('product_id', $product->id)->where('price', $product->price)->first();

            // if ($log) {
            //     $result = $log->count - $data['value'];

            //     if ($result == 0)
            //         $log->delete();
            //     else
            //         $log->count = $result;

            //     $log->save();
            // }

            $product->save();
            $exchange->save();

            DB::commit();

            // return success response
            return $this->success('Exchange created successfully', 200);
        } catch (\Exception $e) {
            return $this->log($e);
        }
    }

    /**
     * @group Exchanges
     * 
     * update exchange
     * 
     * @bodyParam id integer required The id of the exchange. Example: 1
     * @bodyParam product_id integer nullable The id of the product. Example: 1
     * @bodyParam partner_id integer nullable The id of the partner. Example: 1
     * @bodyParam value integer nullable The value of the exchange. Example: 1000
     * 
     * @response {
     * "result": "Exchange updated successfully",
     * }
     * 
     * @param  EditExchangesRequest $request
     * @return JsonResponse
     */

    public function update(EditExchangesRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            DB::beginTransaction();

            // get exchange data and update
            $exchange = Exchange::find($data['id']);
            $exchange->product_id = $data['product_id'] ?? $exchange->product_id;
            $exchange->partner_id = $data['partner_id'] ?? $exchange->partner_id;
            $exchange->value = $data['value'] ?? $exchange->value;

            if (isset($data['value'])) {

                // product
                $product = $exchange->products;

                $usd = Nbu::orderBy('id', 'desc')->first()->nbu_cell_price;

                if ($product->cyrrency == 0) {
                    $exchange->price_uzs = $data['value'] * $product->price;
                    $exchange->price_usd = round(($data['value'] * $product->price) / $usd, 2);
                } elseif ($product->cyrrency == 1) {
                    $exchange->price_uzs = $data['value'] * ($product->price * $usd);
                    $exchange->price_usd = $data['value'] * $product->price;
                }

                // update product count
                $result = $product->count - $data['value'];

                if ($result < 0)
                    return $this->error('Insufficient stock!', 400);

                $product->count = $result;

                $log = ProductsPriceLog::where('product_id', $product->id)->where('price', $product->price)->first();

                if ($log) {
                    $result = $log->count - $data['value'];

                    if ($result == 0)
                        $log->delete();
                    else
                        $log->count = $result;

                    $log->save();
                }

                $product->save();
            }

            $exchange->save();

            DB::commit();

            // return success response
            return $this->success('Exchange updated successfully', 200);
        } catch (\Exception $e) {
            return $this->log($e);
        }
    }

    /**
     * @group Exchanges
     * 
     * delete exchange
     * 
     * @bodyParam id integer required The id of the exchange. Example: 1
     * 
     * @response {
     * "result": "Exchange deleted successfully",
     * }
     * 
     * @param  DeleteExchangesRequest $request
     * @return JsonResponse
     */

    public function delete(DeleteExchangesRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $exchange = Exchange::find($data['id']);
            $exchange->delete();

            // return success response
            return $this->success('Exchange deleted successfully', 200);
        } catch (\Exception $e) {
            return $this->log($e);
        }
    }

    /**
     * @group Exchanges
     * 
     * download exchanges
     * 
     * @bodyParam id integer required The id of the partner. Example: 1
     * @bodyParam from_date string required The from_date of the exchange. Example: 2023-06-15 00:00
     * @bodyParam to_date string required The to_date of the exchange. Example: 2023-06-15 00:00
     * 
     * 
     */

    // public function downpdf(Request $request)
    // {
    //     try {
    //         //save to excel use from_date and to_date and use Maatwebsite\Excel\Concerns\FromCollection

    //         return Excel::download(new ExportExchanges($request), $request['from_date'] . 'exchanges.xlsx');
    //     } catch (\Exception $e) {
    //         return $this->log($e);
    //     }
    // }
}
