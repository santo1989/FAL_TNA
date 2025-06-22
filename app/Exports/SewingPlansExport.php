<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SewingPlansExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data->map(function ($item) {
            return [
                'Month' => \Carbon\Carbon::parse($item->production_plan)->format('M Y'),
                'Buyer' => $item->buyer,
                'Style' => $item->style,
                'Shipment Date' => $item->shipment_date
                    ? \Carbon\Carbon::parse($item->shipment_date)->format('d M Y')
                    : 'N/A',
                'Order Quantity' => $item->order_quantity,
                'Total Plan Quantity' => $item->total_plan_quantity_row, // Updated field name
                'Total Sewing Quantity' => $item->total_sewing_quantity,
                'Remain Sewing Quantity' => $item->remain_sewing,
                'Remain Plan Quantity' => $item->remain_plan,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Month',
            'Buyer',
            'Style',
            'Shipment Date',
            'Order Quantity',
            'Total Plan Quantity',
            'Total Sewing Quantity',
            'Remain Sewing Quantity',
            'Remain Plan Quantity',
        ];
    }
}
