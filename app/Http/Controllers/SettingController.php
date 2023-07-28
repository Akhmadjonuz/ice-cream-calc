<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    use HttpResponses;

    /**
     * @group Settings
     * 
     * Get all settings
     * 
     * @return JsonResponse
     */

    public function getSettings(Request $request): JsonResponse
    {
        try {
            $settings = Setting::all();

            return $this->success($settings);

        } catch (\Exception $e) {
            return $this->log($e);
        }
    }
}
