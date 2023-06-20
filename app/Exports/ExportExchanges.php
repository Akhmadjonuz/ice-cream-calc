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
        $exchanges = Exchange::select('name', 'type', 'value', 'amount', 'all_amount', 'given_amount')->where('partner_id', $this->request->id)->whereBetween('created_at', [$this->request->from_date, $this->request->to_date])->get();
        // add data to end of collection
        $exchanges->push([
            'name' => 'Jami',
            'type' => 'Jami',
            'value' => $exchanges->sum('value'),
            'amount' => $exchanges->sum('amount'),
            'all_amount' => $exchanges->sum('all_amount'),
            'given_amount' => $exchanges->sum('given_amount'),
        ]);

        return $exchanges;
    }
}
