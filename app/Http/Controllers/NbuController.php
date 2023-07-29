<?php

namespace App\Http\Controllers;

use App\Models\Nbu;
use App\Services\NbuService;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class NbuController extends Controller
{
    use HttpResponses;

    public function save(): JsonResponse
    {
        try {
            $service = new NbuService();

            DB::beginTransaction();

            $nbu = new Nbu();
            $nbu->code = 'USD';
            $nbu->nbu_cell_price = $service->getUsd() ?? 11400;
            $nbu->save();

            DB::commit();

            return $this->success('NBU saved successfully');
        } catch (\Exception $e) {
            return $this->log($e);
        }
    }
}