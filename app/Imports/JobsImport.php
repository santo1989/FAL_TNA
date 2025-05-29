<?php

namespace App\Imports;

use App\Models\Job;
use App\Models\Buyer;
use App\Models\Shipment;
use App\Models\SewingBalance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class JobsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use SkipsErrors;

    private $processedRows = 0;
    private $failedRows = [];
    private $batchId;

    public function __construct()
    {
        $this->batchId = 'FAL-' . date('y') . '-' . uniqid();
    }

    public function model(array $row)
    {
        Log::info('JobsImport: Processing new row', ['row_data' => $row]);
        DB::enableQueryLog();
        $normalizedRow = [];

        try {
            $normalizedRow = $this->normalizeKeys($row);
            $this->validateEssentialFields($normalizedRow);
            $buyer = $this->processBuyer($normalizedRow);
            $parsedOrderRef = $this->parseOrderReference($normalizedRow['orderstylepo'] ?? '');

            $sequence = $this->processedRows + count($this->failedRows) + 1;
            $jobNo = $this->generateJobNumber($sequence);

            $jobAttributes = $this->mapJobAttributes($normalizedRow, $buyer->id, $jobNo, $parsedOrderRef);
            $job = $this->updateOrCreateJob($jobNo, $normalizedRow, $jobAttributes);

            $this->processShipment($normalizedRow, $job);
            $this->processSewingBalance($normalizedRow, $job);

            $this->processedRows++;
            return $job;
        } catch (\Exception $e) {
            $this->logError($e, $normalizedRow);
            return null;
        } finally {
            DB::disableQueryLog();
        }
    }

    private function normalizeKeys(array $row): array
    {
        $keyMap = [
            'buyer' => 'buyer',
            'order_style_po' => 'orderstylepo',
            'orderstylepo' => 'orderstylepo',
            'department' => 'department',
            'dept' => 'department',
            'item' => 'item',
            'destination' => 'destination',
            'dest' => 'destination',
            'order_quantity' => 'orderquantity',
            'orderquantity' => 'orderquantity',
            'sewing_balance' => 'sewingbalance',
            'sewingbalance' => 'sewingbalance',
            'shipment_plan' => 'shipmentplan',
            'shipmentplan' => 'shipmentplan',
            'ins_date' => 'insdate',
            'insdate' => 'insdate',
            'delivery_date' => 'deliverydate',
            'deliverydate' => 'deliverydate',
            'target_smv' => 'targetsmv',
            'targetsmv' => 'targetsmv',
            'production_minutes' => 'productionminutes',
            'productionminutes' => 'productionminutes',
            'production_balance' => 'productionbalance',
            'productionbalance' => 'productionbalance',
            'price' => 'price',
            'total_amount' => 'totalamount',
            'totalamount' => 'totalamount',
            'cm_pc' => 'cmpc',
            'cmpc' => 'cmpc',
            'total_cm' => 'totalcm',
            'totalcm' => 'totalcm',
            'consumption' => 'consumption',
            'fabric_qnty' => 'fabricqnty',
            'fabricqnty' => 'fabricqnty',
            'fabrication' => 'fabrication',
            'order_received_date' => 'orderreceiveddate',
            'orderreceiveddate' => 'orderreceiveddate',
            'remarks' => 'remarks',
            'shipped_qty' => 'shippedqty',
            'shippedqty' => 'shippedqty',
            'ex_factory_date' => 'exfactorydate',
            'exfactorydate' => 'exfactorydate',
            'shipped_value' => 'shippedvalue',
            'shippedvalue' => 'shippedvalue',
            'excess_short_shipment_qty' => 'excessshortshipmentqty',
            'excessshortshipmentqty' => 'excessshortshipmentqty',
            'excess_short_shipment_value' => 'excessshortshipmentvalue',
            'excessshortshipmentvalue' => 'excessshortshipmentvalue',
            'delivery_status' => 'deliverystatus',
            'deliverystatus' => 'deliverystatus',
            'color' => 'color',
            'size' => 'size',
        ];

        return collect($row)->mapWithKeys(function ($value, $key) use ($keyMap) {
            $normalizedKey = strtolower(preg_replace('/[^a-z0-9_]/', '', trim($key)));
            return [$keyMap[$normalizedKey] ?? $normalizedKey => $value];
        })->toArray();
    }

    private function parseOrderReference(string $reference): array
    {
        // Handle comma-separated references
        if (strpos($reference, ',') !== false) {
            $parts = array_map('trim', explode(',', $reference));
            return [
                'style' => $parts[0] ?? 'N/A',
                'po' => $parts[1] ?? 'N/A'
            ];
        }

        // Extract PO number with flexible patterns
        $po = null;
        if (preg_match('/\b(PO[- ]*[\dA-Z]+)\b/i', $reference, $poMatches)) {
            $po = $poMatches[0];
        }

        $style = trim(str_replace($po ?? '', '', $reference));

        if (empty($style)) {
            $style = $reference;
        }

        if (empty($po)) {
            $po = $reference;
        }

        return [
            'style' => $style,
            'po' => $po
        ];
    }

    private function generateJobNumber(int $sequence): string
    {
        return $this->batchId . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    private function mapJobAttributes(array $row, int $buyerId, string $jobNo, array $parsedRef): array
    {
        $numericFields = [
            'orderquantity',
            'targetsmv',
            'productionminutes',
            'price',
            'totalamount',
            'cmpc',
            'totalcm',
            'consumption',
            'fabricqnty'
        ];

        foreach ($numericFields as $field) {
            $row[$field] = is_numeric(str_replace(',', '', $row[$field] ?? '0'))
                ? (float) str_replace(',', '', $row[$field])
                : 0;
        }

        $attributes = [
            'company_id' => 3,
            'division_id' => 2,
            'buyer_id' => $buyerId,
            'job_no' => $jobNo,
            'batch_id' => $this->batchId,
            'style' => $parsedRef['style'] ?? 'N/A',
            'po' => $parsedRef['po'] ?? 'N/A',
            'department' => $row['department'] ?? null,
            'item' => $row['item'] ?? null,
            'destination' => $row['destination'] ?? null,
            'color' => $row['color'] ?? 'ALL',
            'size' => $row['size'] ?? 'ALL',
            'order_quantity' => $row['orderquantity'],
            'delivery_date' => $this->parseDate($row['deliverydate']),
            'target_smv' => $row['targetsmv'],
            'production_minutes' => $row['productionminutes'],
            'unit_price' => $row['price'],
            'total_value' => $row['totalamount'],
            'cm_pc' => $row['cmpc'],
            'total_cm' => $row['totalcm'],
            'consumption_dzn' => $row['consumption'],
            'fabric_qnty' => $row['fabricqnty'],
            'fabrication' => $row['fabrication'] ?? '',
            'order_received_date' => $this->parseDate($row['orderreceiveddate'] ?? null),
            'remarks' => $row['remarks'] ?? null
        ];

        Log::debug('JobsImport: Mapped Job Attributes', $attributes);
        return $attributes;
    }

    private function parseDate($value): ?\Carbon\Carbon
    {
        if (empty($value)) {
            return null;
        }

        // Handle Excel numeric dates
        if (is_numeric($value)) {
            try {
                return \Carbon\Carbon::instance(
                    \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float)$value)
                );
            } catch (\Exception $e) {
                Log::warning("Excel date conversion failed: {$value}");
            }
        }

        // Try various string formats
        $formats = [
            'Y-m-d H:i:s',
            'Y-m-d',
            'm/d/Y H:i:s',
            'm/d/Y',
            'd.m.Y H:i:s',
            'd.m.Y',
            'd/m/Y H:i:s',
            'd/m/Y',
        ];

        foreach ($formats as $format) {
            try {
                return \Carbon\Carbon::createFromFormat($format, $value);
            } catch (\Exception $e) {
                continue;
            }
        }

        Log::warning("Date parsing failed for value: {$value}");
        return null;
    }

    public function rules(): array
    {
        return [
            '*.buyer' => 'nullable|string|max:255',
            '*.orderstylepo' => 'nullable|string',
            '*.orderquantity' => 'nullable|numeric|min:0',
            '*.deliverydate' => 'nullable|date',
            '*.targetsmv' => 'nullable|numeric|min:0',
            '*.price' => 'nullable|numeric|min:0',
            '*.totalamount' => 'nullable|numeric|min:0',
            '*.cmpc' => 'nullable|numeric|min:0',
            '*.productionminutes' => 'nullable|numeric|min:0'
        ];
    }

    private function logError(\Exception $e, array $row): void
    {
        $rowNumber = $this->processedRows + count($this->failedRows) + 1;
        $errorMessage = "Row {$rowNumber} failed: " . $e->getMessage();

        Log::error($errorMessage, [
            'raw_row' => $row,
            'normalized_data' => $this->normalizeKeys($row),
            'trace' => $e->getTraceAsString()
        ]);

        $this->failedRows[] = [
            'row' => $rowNumber,
            'error' => $errorMessage
        ];
    }

    private function validateEssentialFields(array $row): void
    {
        if (!isset($row['buyer']) || empty($row['buyer'])) {
            throw new \Exception("Missing or empty 'buyer' field");
        }
        if (!isset($row['orderstylepo']) || empty($row['orderstylepo'])) {
            throw new \Exception("Missing or empty 'orderstylepo' field");
        }
        if (!isset($row['orderquantity']) || !is_numeric(str_replace(',', '', $row['orderquantity']))) {
            throw new \Exception("Invalid or missing 'orderquantity' field");
        }
    }

    private function processBuyer(array $row): Buyer
    {
        Log::info('processBuyer: Attempting to create/update buyer', ['buyer_name' => $row['buyer']]);
        try {
            $buyer = Buyer::lockForUpdate()->firstOrCreate(
                ['name' => strtoupper($row['buyer'])],
                [
                    'division_id' => 2,
                    'company_id' => 3,
                    'company_name' => 'FAL - Factory',
                    'division_name' => 'Factory'
                ]
            );
            Log::info('processBuyer: Buyer created/updated successfully', ['buyer_id' => $buyer->id]);
            return $buyer;
        } catch (\Exception $e) {
            Log::error('processBuyer: Error creating/updating buyer', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    private function updateOrCreateJob(string $jobNo, array $row, array $jobAttributes): Job
    {
        Log::debug('JobsImport: Attempting Job updateOrCreate', [
            'find_criteria' => ['job_no' => $jobNo],
            'update_attributes' => $jobAttributes
        ]);

        try {
            $job = Job::updateOrCreate(
                ['job_no' => $jobNo],
                $jobAttributes
            );

            $logMessage = $job->wasRecentlyCreated ? 'Job created successfully' : 'Job updated successfully';
            Log::info("JobsImport: {$logMessage}", ['job_id' => $job->id, 'job_no' => $job->job_no]);
            return $job;
        } catch (\Exception $e) {
            Log::error('JobsImport: Error during Job updateOrCreate', [
                'error' => $e->getMessage(),
                'query_log' => DB::getQueryLog()
            ]);
            throw $e;
        }
    }

    private function processShipment(array $row, Job $job): void
    {
        Log::info('processShipment: Attempting to create/update shipment', ['job_id' => $job->id]);
        try {
            $shipmentAttributes = [
                'shipped_qty' => $this->parseNumeric($row['shippedqty'] ?? 0),
                'ex_factory_date' => $this->parseDate($row['exfactorydate'] ?? null),
                'shipped_value' => $this->parseNumeric($row['shippedvalue'] ?? 0),
                'excess_short_shipment_qty' => $this->parseNumeric($row['excessshortshipmentqty'] ?? 0),
                'excess_short_shipment_value' => $this->parseNumeric($row['excessshortshipmentvalue'] ?? 0),
                'delivery_status' => $row['deliverystatus'] ?? 'Pending'
            ];

            $shipment = Shipment::updateOrCreate(
                ['job_id' => $job->id],
                $shipmentAttributes
            );
            Log::info("processShipment: Shipment processed successfully", ['shipment_id' => $shipment->id]);
        } catch (\Exception $e) {
            Log::error('processShipment: Error creating/updating shipment', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    private function processSewingBalance(array $row, Job $job): void
    {
        Log::info('processSewingBalance: Attempting to create/update sewing balance', ['job_id' => $job->id]);
        try {
            $sewingBalanceAttributes = [
                'sewing_balance' => $this->parseNumeric($row['sewingbalance'] ?? 0),
                'production_plan' => $this->parseDate($row['shipmentplan'] ?? null),
                'production_min_balance' => $this->parseNumeric($row['productionbalance'] ?? 0)
            ];

            $sewingBalance = SewingBalance::updateOrCreate(
                ['job_id' => $job->id],
                $sewingBalanceAttributes
            );
            Log::info("processSewingBalance: Sewing balance processed successfully", ['id' => $sewingBalance->id]);
        } catch (\Exception $e) {
            Log::error('processSewingBalance: Error creating/updating sewing balance', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    private function parseNumeric($value): float
    {
        return is_numeric(str_replace(',', '', $value))
            ? (float) str_replace(',', '', $value)
            : 0;
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
        Log::error("Import error (from onError callback): {$e->getMessage()}", [
            'trace' => $e->getTraceAsString()
        ]);
    }
}
