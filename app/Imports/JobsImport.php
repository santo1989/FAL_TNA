<?php

namespace App\Imports;

use App\Models\Job;
use App\Models\Buyer;
use App\Models\Shipment;
use App\Models\SewingBalance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class JobsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, WithBatchInserts
{
    
    use SkipsErrors;

    private $processedRows = 0;
    private $failedRows = [];

    public function model(array $row)
    {
        // dd($row); // Debugging line to inspect the row data
         
        DB::beginTransaction();

        try {
            $normalizedRow = $this->normalizeKeys($row);

            // 1. Buyer Handling
            $buyer = $this->processBuyer($normalizedRow);

            // 2. Job Number Generation
            $jobNo = $this->generateJobNumber();

            // 3. Parse Combined Fields
            $parsedOrderRef = $this->parseOrderReference($normalizedRow['or_no_ref_style_po'] ?? '');

            // dd($parsedOrderRef); // Debugging line to inspect parsed order reference

            // 4. Job Creation
            $job = Job::updateOrCreate(
                [
                    'job_no' => $jobNo,
                    'color' => $normalizedRow['color'] ?? 'ALL',
                    'size' => $normalizedRow['size'] ?? 'ALL'
                ],
                $this->mapJobAttributes($normalizedRow, $buyer->id, $jobNo, $parsedOrderRef)
            );

            // 5. Shipment Handling
            $this->processShipment($normalizedRow, $job);

            // 6. Sewing Balance
            $this->processSewingBalance($normalizedRow, $job);

            DB::commit();
            $this->processedRows++;

            return $job;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->logError($e);
            return null;
        }
    }

    private function normalizeKeys(array $row): array
    {
        $keyMap = [
            'buyer' => 'buyer',
            'Order_Style_PO' => 'or_no_ref_style_po',
            'department' => 'dept',
            'item' => 'item',
            'destination' => 'dest',
            'order quantity' => 'order_quantity',
            'sewing balance' => 'sewing_balance',
            'shipment plan' => 'shipment_plan',
            'ins_date' => 'ins_date',
            'delivery date' => 'delivery_date',
            'target smv' => 'target_smv',
            'production minutes' => 'production_minutes',
            'production balance' => 'production_balance',
            'price' => 'price',
            'total amount' => 'total_amount',
            'cm_pc' => 'cm_pcs',
            'total cm' => 'total_cm',
            'consumption' => 'consumption',
            'fabric qnty.' => 'fabric_qnty',
            'fabrication' => 'fabrication',
            'order received date' => 'order_received_date',
            'remarks' => 'remarks',
            'shipped qty' => 'shipped_qty',
            'ex-factory date' => 'ex_factory_date',
            'shipped value' => 'shipped_value',
            'excess short shipment qty' => 'excess_short_shipment_qty',
            'excess short shipment value' => 'excess_short_shipment_value',
            'delivery status' => 'delivery_status'
        ];

        return collect($row)->mapWithKeys(function ($value, $key) use ($keyMap) {
            $normalizedKey = strtolower(trim($key));
            return [$keyMap[$normalizedKey] ?? $normalizedKey => $value];
        })->toArray();
    }

    private function processBuyer(array $row): Buyer
    {
        return Buyer::lockForUpdate()->firstOrCreate(
            ['name' => strtoupper($row['buyer'])],
            [
                'division_id' => 2,
                'company_id' => 3,
                'company_name' => 'FAL - Factory',
                'division_name' => 'Factory'
            ]
        );
    }

    private function generateJobNumber(): string
    {
        $latestJob = Job::lockForUpdate()->latest('id')->first();
        $sequence = $latestJob ? $latestJob->id + 1 : 1;
        return 'FAL-' . date('y') . '-' . str_pad($sequence, 6, '0', STR_PAD_LEFT);
    }

    private function parseOrderReference(string $reference): array
    {
        preg_match_all('/PO-?\d+/', $reference, $poMatches);
        $po = $poMatches[0][0] ?? null;

        $stylePart = trim(preg_replace('/PO-?\d+/', '', $reference));
        $stylePart = preg_replace('/\s{2,}/', ' ', $stylePart);

        return [
            'style' => $stylePart,
            'po' => $po,
        ];
    }

    private function mapJobAttributes(array $row, int $buyerId, string $jobNo, array $parsedRef): array
    {
        return [
            'company_id' => 3,
            'division_id' => 2,
            'buyer_id' => $buyerId,
            'style' => $parsedRef['style'],
            'po' => $parsedRef['po'],
            'department' => $row['dept'] ?? null,
            'item' => $row['item'] ?? null,
            'destination' => $row['dest'] ?? null,
            'order_quantity' => $row['order_quantity'] ?? 0,
            'delivery_date' => $this->parseDate($row['delivery_date']),
            'target_smv' => $row['target_smv'] ?? 0,
            'production_minutes' => $row['production_minutes'] ?? 0,
            'unit_price' => $row['price'] ?? 0,
            'total_value' => $row['total_amount'] ?? 0,
            'cm_pc' => $row['cm_pcs'] ?? 0,
            'total_cm' => $row['total_cm'] ?? 0,
            'consumption_dzn' => $row['consumption'] ?? 0,
            'fabric_qnty' => $row['fabric_qnty'] ?? 0,
            // In mapJobAttributes()
            'fabrication' => $row['fabrication'] ?? '', // Corrected from 'item_description'
            'order_received_date' => $this->parseDate($row['order_received_date'] ?? null),
            'remarks' => $row['remarks'] ?? null
        ];
    }

    private function processShipment(array $row, Job $job): void
    {
        Shipment::updateOrCreate(
            [
                'job_id' => $job->id,
                'color' => $row['color'] ?? 'ALL',
                'size' => $row['size'] ?? 'ALL'
            ],
            [
                'shipped_qty' => $row['shipped_qty'] ?? 0,
                'ex_factory_date' => $this->parseDate($row['ex_factory_date'] ?? null),
                'shipped_value' => $row['shipped_value'] ?? 0,
                'excess_short_shipment_qty' => $row['excess_short_shipment_qty'] ?? 0,
                'excess_short_shipment_value' => $row['excess_short_shipment_value'] ?? 0,
                'delivery_status' => $row['delivery_status'] ?? 'Pending'
            ]
        );
    }

    private function processSewingBalance(array $row, Job $job): void
    {
        SewingBalance::updateOrCreate(
            [
                'job_id' => $job->id,
                'color' => $row['color'] ?? 'ALL',
                'size' => $row['size'] ?? 'ALL'
            ],
            [
                'sewing_balance' => $row['sewing_balance'] ?? 0,
                'production_plan' => $this->parseDate($row['shipment_plan'] ?? null),
                'production_min_balance' => $row['production_balance'] ?? 0
            ]
        );
    }

    private function parseDate($value)
    {
        if (empty($value) || $value === '0000-00-00 00:00:00') {
            return null;
        }
        try {
            return \Carbon\Carbon::parse($value);
        } catch (\Exception $e) {
            Log::error("Failed to parse date: {$value}");
            return null;
        }
    }

    public function rules(): array
    {
        return [
            '*.buyer' => 'required|string|max:255',
            '*.or_no_ref_style_po' => 'required|string',
            '*.order_quantity' => 'required|numeric|min:0',
            // Make other fields nullable
            '*.delivery_date' => 'nullable|date',
            '*.target_smv' => 'nullable|numeric|min:0',
            '*.price' => 'nullable|numeric|min:0',
            '*.total_amount' => 'nullable|numeric|min:0',
            '*.cm_pcs' => 'nullable|numeric|min:0',
            '*.production_minutes' => 'nullable|numeric|min:0'
        ];
    }

    private function logError(\Exception $e, array $row): void
    {
        $rowNumber = $this->processedRows + count($this->failedRows) + 1;
        $this->failedRows[] = [
            'row' => $rowNumber,
            'data' => $row,
            'error' => $e->getMessage()
        ];
        Log::error("Row {$rowNumber} failed: " . $e->getMessage(), [
            'row_data' => $row,
            'trace' => $e->getTraceAsString()
        ]);
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function getProcessedRows(): int
    {
        return $this->processedRows;
    }

    public function getFailedRows(): array
    {
        return $this->failedRows;
    }

    public function onError(\Throwable $e)
    {
        // Handled in main error handling
        Log::error("Import error: {$e->getMessage()}", [
            'trace' => $e->getTraceAsString()
        ]);
    }
}
