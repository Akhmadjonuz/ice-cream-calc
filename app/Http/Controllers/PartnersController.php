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
use Illuminate\Support\Facades\DB;

class PartnersController extends Controller
{
    use HttpResponses;

    /**
     * @group Partners
     * 
     * create partner
     * 
     * @bodyParam name string required The name of the partner. Example: John Doe
     * @bodyParam phone_number string nullable The phone number of the partner. Example: 998901234567
     * @bodyParam address string nullable The address of the partner. Example: 123, Main Street, New York
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
     * @param Request $request
     * @return JsonResponse
     */

    public function get(Request $request): JsonResponse
    {
        try {
            return $this->success(Partner::all(), 200);
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
     * @bodyParam name string nullable The name of the partner. Example: John Doe
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

            DB::beginTransaction();

            $partner = Partner::find($data['id']);
            $partner->name = $data['name'] ?? $partner->name;
            $partner->phone_number = $data['phone_number'] ?? $partner->phone_number;
            $partner->address = $data['address'] ?? $partner->address;
            $partner->save();

            DB::commit();

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
            // $data = $request->validated();

            // // delete partner from exchange table
            // Exchange::where('partner_id', $data['id'])->delete();

            // // delete partner from debt table
            // Debt::where('partner_id', $data['id'])->delete();

            // // delete partner from partner table
            // Partner::where('id', $data['id'])->delete();

            // return success response
            return $this->success('Partner does not deleted', 200);
        } catch (\Exception $e) {
            return $this->log($e);
        }
    }
}