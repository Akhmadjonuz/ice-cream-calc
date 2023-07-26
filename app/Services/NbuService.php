<?php

namespace App\Services\NbuService;

use Illuminate\Support\Facades\Http;

/**
 * Summary of NbuService
 */
class NbuService
{
    /**
     * Summary of getNbu
     * @return array
     */
    private function getNbu(): array
    {
        $response = Http::get('https://bank.gov.ua/NBUStatService/v1/statdirectory/exchange?json');

        if ($response->successful())
            return $response->json();
        else
            return [];
    }

    /**
     * Summary of getUsd
     * @return float
     */

    public function getUsd(): float
    {
        $nbu = $this->getNbu();

        foreach ($nbu as $item) {
            if ($item['code'] == 'USD')
                return $item['nbu_cell_price'];
        }

        return 0;
    }
}