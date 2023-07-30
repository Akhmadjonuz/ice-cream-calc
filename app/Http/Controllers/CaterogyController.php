<?php

namespace App\Http\Controllers;

use App\Http\Requests\Caterogy\CreateCaterogyRequest;
use App\Http\Requests\Caterogy\UpdateCaterogyRequest;
use App\Models\Caterogy;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CaterogyController extends Controller
{
    use HttpResponses;


    /**
     * @group Caterogies
     * 
     * get caterogies
     * 
     * @return JsonResponse
     */

    public function getCaterogies(): JsonResponse
    {
        try {
            $caterogies = Caterogy::all();
            return $this->success($caterogies);
        } catch (\Exception $e) {
            return $this->e($e);
        }
    }

    /**
     * @group Caterogies
     *
     * create caterogy
     * 
     * @bodyParam name string required
     * 
     * @param CreateCaterogyRequest $request
     * @return JsonResponse
     */

    public function createCaterogy(CreateCaterogyRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            DB::beginTransaction();

            $caterogy = new Caterogy();
            $caterogy->name = $data['name'];
            $caterogy->save();

            DB::commit();

            return $this->success('Caterogy created successfully');
        } catch (\Exception $e) {
            return $this->log($e);
        }
    }

    /**
     * @group Caterogies
     *
     * update caterogy
     * 
     * @bodyParam id int required
     * @bodyParam name string nullable
     * 
     * @param UpdateCaterogyRequest $request
     * @return JsonResponse
     */

    public function updateCaterogy(UpdateCaterogyRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            if (!empty($data['type']))
                return $this->error('Type is not editable', 401);

            DB::beginTransaction();

            $caterogy = Caterogy::find($data['id']);
            $caterogy->name = $data['name'] ?? $caterogy->name;
            $caterogy->save();

            DB::commit();

            return $this->success('Caterogy updated successfully');
        } catch (\Exception $e) {
            return $this->log($e);
        }
    }
}