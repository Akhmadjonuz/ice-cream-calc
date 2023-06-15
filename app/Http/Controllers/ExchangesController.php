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
     * @bodyParam name string nullable The name of the exchange. Example: John Doe
     * @bodyParam partner_id integer required The id of the partner. Example: 1
     * @bodyParam value string nullable The value of the exchange. Example: 1000
     * @bodyParam type string nullable The type of the exchange. Example: Tonna, metr, M3, M2
     * @bodyParam car string nullable The car of the exchange. Example: 50A777AA
     * @bodyParam amount integer nullable The amount of the exchange. Example: 1
     * @bodyParam given_amount nullable required The given amount of the exchange. Example: 1000
     * @bodyParam other boolean required The other of the exchange. Example: false
     * @bodyParam created_at date nullable The created_at of the exchange. Example: 2023-06-15 00:00:00
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

            $partner = Partner::where('id', $data['partner_id'])->first();
            if ($partner->type !== 'partner')
                return $this->error('Partner type not partner', 400);

            $exchange = new Exchange();
            $exchange->name = $data['name'] ?? null;
            $exchange->partner_id = $data['partner_id'];
            $exchange->value = $data['value'] ?? null;
            $exchange->type = $data['type'] ?? null;
            $exchange->car = $data['car'] ?? null;
            $exchange->amount = $data['amount'] ?? 0;
            $exchange->given_amount = $data['given_amount'] ?? 0;

            if (!empty($data['created_at']))
                $exchange->created_at = $data['created_at'];

            // if other is true and not debts in db then return error
            if ($data['other'] == true) {
                $exchanges = Exchange::where('partner_id', $data['partner_id'])->get();
                $summ = 0;
                foreach ($exchanges as $item) {
                    if ($item->amount !== $item->given_amount && $item->other == false)
                        $summ += $item->amount - $item->given_amount;
                }
                if ($summ == 0)
                    return $this->error('Exchange other is true but not debts in db', 400);
            }


            $exchange->other = $data['other'];
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
     * @bodyParam name string nullable The name of the exchange. Example: John Doe
     * @bodyParam value string nullable The value of the exchange. Example: 1000
     * @bodyParam type string nullable The type of the exchange. Example: Tonna, metr, M3, M2
     * @bodyParam amount integer required The amount of the exchange. Example: 1
     * @bodyParam given_amount nullable required The given amount of the exchange. Example: 1000
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
            // save to pdf
            // $pdf = PDF::loadView('pdf.exchanges', compact('exchanges'));
            // $pdf->save(storage_path() . '/app/public/exchanges.pdf');


            //save to excel use from_date and to_date and use Maatwebsite\Excel\Concerns\FromCollection

            return Excel::download(new ExportExchanges($request), $request['from_date'] . 'exchanges.xlsx');
        } catch (\Exception $e) {
            return $this->log($e);
        }
    }
}