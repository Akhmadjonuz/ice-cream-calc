<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpensesController extends Controller
{
    use HttpResponses;

    /** 
     * @group Expenses
     * 
     * Get all expenses
     * 
     * @return JsonResponse
     */

    public function getExpenses(Request $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $from_date = $data['from_date'] ?? date('Y-m-d 00:00');
            $to_date = $data['to_date'] ?? date('Y-m-d 23:59:59');

            $query = Expense::query()->orderBy('id', 'desc');

            if (isset($data['product_id']))
                $query->where('product_id', $data['product_id']);

            if (isset($data['material_id']))
                $query->where('product_id', $data['material_id']);

            $query->whereBetween('created_at', [$from_date, $to_date])->orderBy('id', 'desc')->get();

            return $this->success($query->get(), 200);
        } catch (\Exception $e) {
            return $this->log($e);
        }
    }
}