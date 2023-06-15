<?php

namespace App\Exports;

use App\Models\Exchange;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportExchanges implements FromCollection
{

    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Exchange::select('name', 'value', 'type', 'amount', 'given_amount')->where('partner_id', $this->request->id)->whereBetween('created_at', [$this->request->from_date, $this->request->to_date])->get();
    }
}
