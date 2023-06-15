<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDebtsRequest;
use App\Models\Debt;
use App\Models\Partner;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DebtsController extends Controller
{
    use HttpResponses;

    /**
     * @group Debts
     * 
     * create new debt
     * 
     * @bodyParam name string required The name of the debt. Example: John Doe
     * @bodyParam partner_id integer required The id of the partner. Example: 1
     * @bodyParam value string required The value of the debt. Example: 1000
     * @bodyParam type string required The type of the debt. Example: Tonna, metr, M3, M2
     * @bodyParam amount integer required The amount of the debt. Example: 1
     * @bodyParam given_amount integer required The given amount of the debt. Example: 1000
     * @bodyParam other boolean required The other of the debt. Example: false
     * @bodyParam created_at date nullable The created_at of the debt. Example: 2023-06-15 00:00:00
     * 
     * @response {
     * "result": "Debt created successfully",
     * }
     * 
     * @param CreateDebtsRequest $request
     * @return JsonResponse 
     */

    public function create(CreateDebtsRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // if user type is partner quuit
            $check = Partner::where('id', $data['partner_id'])->first();
            if ($check->type == 'partner')
                return $this->error('You can not create a debt for a partner', 400);

            // create a new debt
            $debt = new Debt();
            $debt->partner_id = $data['partner_id'];
            $debt->name = $data['name'] ?? null;
            $debt->value = $data['value'] ?? null;
            $debt->type = $data['type'] ?? null;
            $debt->amount = $data['amount'] ?? 0;
            $debt->given_amount = $data['given_amount'] ?? 0;

            if (!empty($data['created_at']))
                $debt->created_at = $data['created_at'];

            // if other is true and not debts in db then return error
            if ($data['other'] == true) {
                $debts = Debt::where('partner_id', $data['partner_id'])->get();
                $summ = 0;
            }

            $debt->other = $data['other'];
            $debt->save();

            return $this->success('Debt created successfully', 200);
        } catch (\Exception $e) {
            return $this->log($e);
        }
    }
}
