<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePartnersRequest;
use App\Http\Requests\DeletePartnersRequest;
use App\Http\Requests\GetPartnersRequest;
use App\Http\Requests\UpdatePartnersRequest;
use App\Models\Debt;
use App\Models\Exchange;
use App\Traits\HttpResponses;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PartnersController extends Controller
{
    use HttpResponses;

    /**
     * @group Partners
     * 
     * create partner
     * 
     * @bodyParam name string required The name of the partner. Example: John Doe
     * @bodyParam phone_number string required The phone number of the partner. Example: 998901234567
     * @bodyParam address string nullable The address of the partner. Example: 123, Main Street, New York
     * @bodyParam type string required The type of the partner. Example: debtor or partner
     * 
     * 
     * @param CreatePartnersRequest $request
     * @return JsonResponse
     */


    public function create(CreatePartnersRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // check unique phone number
            $check = Partner::where('phone_number', $data['phone_number'])->first();
            if ($check)
                return $this->error('Phone number already exists', 400);

            $partner = new Partner();
            $partner->name = $data['name'];
            $partner->phone_number = $data['phone_number'];
            $partner->address = $data['address'];
            $partner->type = $data['type'];
            $partner->save();

            // return success response
            return $this->success('Partner created successfully', 200);
        } catch (\Exception $e) {
            return $this->log($e);
        }
    }


    /**
     * @group Partners
     * 
     * get all partners
     * 
     * @bodyParam id int nullable The id of the partner. Example: 1
     * @bodyParam type string nullable The type of the partner. Example: debtor or partner
     * @bodyParam from_date date nullable The from date of the partner. Example: 2023-06-02
     * @bodyParam to_date date nullable The to date of the partner. Example: 2023-06-03
     * 
     * 
     * @response {
     *    "result": [
     *       {
     *          "id": 1,
     *         "name": "John Doe",
     *        "phone_number": "998901234567",
     *       "address": "123, Main Street, New York",
     *      "type": "debtor",
     *     "created_at": "2023-06-02T22:23:36.000000Z",
     *     "updated_at": "2023-06-02T22:23:36.000000Z"
     * },
     * "debts": 0,
     * "right": 0,
     *]
     *}
     * 
     * @param GetPartnersRequest $request
     * @return JsonResponse
     */

    public function get(GetPartnersRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();


            // create query builder
            $query = Partner::query()->orderBy('id', 'desc');

            // check if id is set
            if (isset($data['id'])) {
                $from_date = $data['from_date'] ?? date('Y-m-d H:i');
                $to_date = $data['to_date'] ?? date('Y-m-d H:i');
                $debt = 0;
                $right =  0;

                $query->where('id', $data['id'])->with(
                    [
                        'exchanges' => function ($query) use ($from_date, $to_date) {
                            $query->whereBetween('created_at', [$from_date, $to_date])
                                ->orderBy('id', 'desc');
                        }
                    ],
                    [
                        'debts' => function ($query) use ($from_date, $to_date) {
                            $query->whereBetween('created_at', [$from_date, $to_date])
                                ->orderBy('id', 'desc');
                        }
                    ]
                );


                if ($data['type'] == 'partner') {
                    // if given_amount is == amount then we have $debt = 0;
                    $exchanges = Exchange::where('partner_id', $data['id'])->whereBetween('created_at', [$from_date, $to_date])->get();

                    foreach ($exchanges as $exchange) {
                        if ($exchange->amount > $exchange->given_amount && $exchange->other == false)
                            $debt += $exchange->amount - $exchange->given_amount;
                        elseif ($exchange->amount < $exchange->given_amount && $exchange->other == false)
                            $right += $exchange->given_amount - $exchange->amount;
                        elseif ($exchange->other == true)
                            $debt -= $exchange->given_amount;
                    }

                    // come back to this later. get me the value of the other true get me amount
                    $exchanges = $exchanges->where('other', true)->sum('amount');
                    $debt -= $exchanges;
                }

                if ($right > $debt) {
                    $right -= $debt;
                    $debt = 0;
                } elseif ($right < $debt) {
                    $debt -= $right;
                    $right = 0;
                } elseif ($right == $debt)
                    $right = $debt = 0;


                return $this->success(['partner' => $query->first(), 'debt' => $debt, 'right' => $right], 200);
            }

            // check if type is set
            if (isset($data['type']))
                $query->where('type', $data['type']);

            // Return success response
            return $this->success($query->get(), 200);
        } catch (\Exception $e) {
            return $this->log($e);
        }
    }


    /**
     * @group Partners
     * 
     * update partner
     * 
     * @bodyParam id int required The id of the partner. Example: 1
     * @bodyParam name string required The name of the partner. Example: John Doe
     * @bodyParam phone_number string nullable The phone number of the partner. Example: 998901234567
     * @bodyParam address string nullable The address of the partner. Example: 123, Main Street, New York
     * 
     * 
     * @param UpdatePartnersRequest $request
     * @return JsonResponse
     */

    public function update(UpdatePartnersRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $partner = Partner::find($data['id']);
            $partner->name = $data['name'];
            $partner->phone_number = $data['phone_number'];
            $partner->address = $data['address'];
            $partner->save();

            // return success response
            return $this->success('Partner updated successfully', 200);
        } catch (\Exception $e) {
            return $this->log($e);
        }
    }


    /**
     * @group Partners
     * 
     * delete partner
     * 
     * @bodyParam id int required The id of the partner. Example: 1
     * 
     * @param DeletePartnersRequest $request
     * @return JsonResponse
     */

    public function delete(DeletePartnersRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // delete partner from exchange table
            Exchange::where('partner_id', $data['id'])->delete();

            // delete partner from debt table
            Debt::where('partner_id', $data['id'])->delete();

            // delete partner from partner table
            Partner::where('id', $data['id'])->delete();

            // return success response
            return $this->success('Partner deleted successfully', 200);
        } catch (\Exception $e) {
            return $this->log($e);
        }
    }
}
