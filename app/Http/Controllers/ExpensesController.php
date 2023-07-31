<?php

namespace App\Http\Controllers;

use App\Http\Requests\Expenses\GetExpensesRequest;
use App\Models\Expense;
use App\Models\Setting;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpensesController extends Controller
{
    use HttpResponses;

    /** 
     * @group Expenses
     * 
     * Get all expenses
     * 
     * @bodyParam from_date date optional
     * @bodyParam to_date date optional
     * @bodyParam product_id integer optional
     * @bodyParam material_id integer optional
     * 
     * 
     * @param GetExpensesRequest $request
     * @return JsonResponse
     */

    public function getExpenses(GetExpensesRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $from_date = isset($data['from_date']) ? Carbon::parse($data['from_date'])->startOfDay() : Carbon::now()->startOfDay();
            $to_date = isset($data['to_date']) ? Carbon::parse($data['to_date'])->endOfDay() : Carbon::now()->endOfDay();

            $query = Expense::query()
                ->orderBy('id', 'desc');

            if (isset($data['product_id']))
                $query->where('product_id', $data['product_id']);

            if (isset($data['material_id']))
                $query->where('product_id', $data['material_id']);

            $query->whereBetween('created_at', [$from_date, $to_date]);

            $success = [];
            $success['data'] = $query->get();

            $success['used_uzs'] = $query->sum('price_uzs');
            $success['used_usd'] = $query->sum('price_usd');
            $success['maked_products'] = $query->sum('count');
            $success['distinct_products'] = $query->distinct()->count('product_id');
            $success['distinct_materials'] = $query->distinct()->count('material_id') - 1;
            $success['distinct_types'] = $query->distinct()->count('type_id');

            $types = Setting::all();
            $ex = DB::table('expenses')
                ->whereBetween('created_at', [$from_date, $to_date])
                ->get();

            foreach ($types as $type) {
                $success['used_materials'][$type->name] = $ex->where('type_id', $type->id)->sum('value');
            }

            return $this->success($success, 200);
        } catch (\Exception $e) {
            return $this->log($e);
        }
    }
}