<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateExchangesRequest;
use App\Http\Requests\DeleteExchangesRequest;
use App\Http\Requests\EditExchangesRequest;
use App\Models\Exchange;
use App\Models\Partner;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;


class ExchangesController extends Controller
{
    use HttpResponses;

    /**
     * @group Exchanges
     * 
     * create exchange
     * 
     * @bodyParam name string required The name of the exchange. Example: John Doe
     * @bodyParam partner_id integer required The id of the partner. Example: 1
     * @bodyParam value string required The value of the exchange. Example: 1000
     * @bodyParam type string required The type of the exchange. Example: Tonna, metr, M3, M2
     * @bodyParam amount integer required The amount of the exchange. Example: 1
     * @bodyParam given_amount integer required The given amount of the exchange. Example: 1000
     * @bodyParam other string nullable The other of the exchange. Example: 123, Main Street, New York
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

            $partner = Partner::where('id', $data['partner_id'])->first();
            if ($partner->type !== 'partner')
                return $this->error('Partner type not partner', 400);

            $exchange = new Exchange();
            $exchange->name = $data['name'];
            $exchange->partner_id = $data['partner_id'];
            $exchange->value = $data['value'];
            $exchange->type = $data['type'];
            $exchange->amount = $data['amount'];
            $exchange->given_amount = $data['given_amount'];
            $exchange->excess = $data['given_amount'] > 0 ? true : false;
            $exchange->other = $data['other'];
            $exchange->save();

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
     * @bodyParam name string required The name of the exchange. Example: John Doe
     * @bodyParam value string required The value of the exchange. Example: 1000
     * @bodyParam type string required The type of the exchange. Example: Tonna, metr, M3, M2
     * @bodyParam amount integer required The amount of the exchange. Example: 1
     * @bodyParam given_amount integer required The given amount of the exchange. Example: 1000
     * @bodyParam excess boolean required The excess of the exchange. Example: true or false
     * @bodyParam other string nullable The other of the exchange. Example: 123, Main Street, New York
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
            $exchange->excess = $data['given_amount'] > 0 ? true : false;
            $exchange->other = $data['other'];
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
}
