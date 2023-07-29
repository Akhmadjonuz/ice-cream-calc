<?php

namespace App\Http\Controllers;

use App\Exports\ExportExchanges;
use App\Http\Requests\CreateExchangesRequest;
use App\Http\Requests\DeleteExchangesRequest;
use App\Http\Requests\EditExchangesRequest;
use App\Models\Exchange;
use App\Services\NbuService;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ExchangesController extends Controller
{
    use HttpResponses;

    /**
     * @group Exchanges
     * 
     * get all exchanges
     * 
     * @param Request $request
     * @return JsonResponse
     */

    public function getExchanges(Request $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $from_date = $data['from_date'] ?? date('Y-m-d 00:00');
            $to_date = $data['to_date'] ?? date('Y-m-d 23:59:59');

            // create query builder
            $query = Exchange::query()->with(['products', 'partners', 'caterogies', 'settings'])->orderBy('id', 'desc');

            // filter by product
            if (isset($data['product_id']))
                $query->where('product_id', $data['product_id']);

            // filter by partner
            if (isset($data['partner_id']))
                $query->where('partner_id', $data['partner_id']);

            // filter by cyrrency type (UZS or USD) get data from hasMany relation
            if (isset($data['cyrrency']))
                $query->whereHas('products', function ($q) use ($data) {
                    $q->where('cyrrency', $data['cyrrency']);
                });

            // filter by caterogy get data from hasMany relation
            if (isset($data['caterogy_id']))
                $query->whereHas('products', function ($q) use ($data) {
                    $q->where('caterogy_id', $data['caterogy_id']);
                });

            // filter by type get data from hasMany relation
            if (isset($data['type_id']))
                $query->whereHas('products', function ($q) use ($data) {
                    $q->where('type_id', $data['type_id']);
                });

            // filter by from date and to date
            $query->whereBetween('created_at', [$from_date, $to_date])->orderBy('id', 'desc');

            $success = [
                'all_count' => $query->sum('value'),
                'price_uzs_all' => $query->sum('price_uzs'),
                'price_usd_all' => $query->sum('price_usd'),
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

            // get nbu data
            $nbu = new NbuService;
            $usd = $nbu->getUsd();

            $exchange = new Exchange();
            $exchange->product_id = $data['product_id'];
            $exchange->partner_id = $data['partner_id'];
            $exchange->value = $data['value'];

            // product
            $product = $exchange->products;

            if ($product->cyrrency == 0) {
                $exchange->price_uzs = $data['value'] * $product->price;
                $exchange->price_usd = ($data['value'] * $product->price) / $usd;
            } elseif ($product->cyrrency == 1) {
                $exchange->price_uzs = $data['value'] * ($product->price * $usd);
                $exchange->price_usd = $data['value'] * $product->price;
            }

            // update product quantity
            $result = $product->quantity - $data['value'];

            if ($result < 0)
                return $this->error('Insufficient stock!', 400);

            $product->quantity = $result;

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
                // get nbu data
                $nbu = new NbuService;
                $usd = $nbu->getUsd();

                // product
                $product = $exchange->products;

                if ($product->cyrrency == 0) {
                    $exchange->price_uzs = $data['value'] * $product->price;
                    $exchange->price_usd = ($data['value'] * $product->price) / $usd;
                } elseif ($product->cyrrency == 1) {
                    $exchange->price_uzs = $data['value'] * ($product->price * $usd);
                    $exchange->price_usd = $data['value'] * $product->price;
                }

                // update product quantity
                $result = $product->quantity - $data['value'];

                if ($result < 0)
                    return $this->error('Insufficient stock!', 400);

                $product->quantity = $result;

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

    public function downpdf(Request $request)
    {
        try {
            //save to excel use from_date and to_date and use Maatwebsite\Excel\Concerns\FromCollection

            return Excel::download(new ExportExchanges($request), $request['from_date'] . 'exchanges.xlsx');
        } catch (\Exception $e) {
            return $this->log($e);
        }
    }
}