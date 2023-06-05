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
     * create a new debt
     * 
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
            $debt->name = $data['name'];
            $debt->value = $data['value'];
            $debt->type = $data['type'];
            $debt->amount = $data['amount'];
            $debt->given_amount = $data['given_amount'];
            $debt->save();

            return $this->success('Debt created successfully', 200);
        } catch (\Exception $e) {
            return $this->log($e);
        }
    }
}