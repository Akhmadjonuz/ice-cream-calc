<?php

namespace App\Http\Controllers;

use App\Exports\ExportExchanges;
use App\Http\Requests\CreateExchangesRequest;
use App\Http\Requests\DeleteExchangesRequest;
use App\Http\Requests\EditExchangesRequest;
use App\Models\Exchange;
use App\Models\Partner;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExchangesController extends Controller
{
    use HttpResponses;

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
            $exchange->name = $data['name'] ?? 'Pul';
            $exchange->partner_id = $data['partner_id'];
            $exchange->value = $data['value'] ?? null;
            $exchange->type = $data['type'] ?? null;
            $exchange->car = $data['car'] ?? null;
            $exchange->amount = $data['amount'] ?? 0;
            $exchange->given_amount = $data['given_amount'] ?? 0;
            $exchange->all_amount = $data['value'] * $data['amount'] ?? 0;

            if (!empty($data['created_at']))
                $exchange->created_at = $data['created_at'];

            // if other is true and not debts in db then return error
            if ($data['other'] == true) {
                $exchanges = Exchange::where('partner_id', $data['partner_id'])->get();
                $summ = 0;
                foreach ($exchanges as $item) {
                    if ($item->all_amount !== $item->given_amount && $item->other == false)
                        $summ += $item->all_amount - $item->given_amount;
                }
                if ($summ == 0)
                    return $this->error('Exchange other is true but not debts in db', 400);
            }
            $exchange->other = $data['other'];

            $partner = Partner::where('id', $data['partner_id'])->first();

            if ($partner->type == 'partner')
                $exchange->p_type = 'p';
            else
                $exchange->p_type = 'd';

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

            $exchange = Exchange::find($data['id']);
            $exchange->name = $data['name'];
            $exchange->value = $data['value'];
            $exchange->type = $data['type'];
            $exchange->amount = $data['amount'];
            $exchange->given_amount = $data['given_amount'];
            $exchange->save();

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