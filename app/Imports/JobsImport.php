<?php

namespace App\Imports;

use App\Models\Job;
use App\Models\Buyer;
use App\Models\Shipment;
use App\Models\SewingBalance;
use App\Models\SewingPlan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Validators\ValidationException;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class JobsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsEmptyRows, WithEvents, WithCalculatedFormulas
{
    use SkipsErrors, RegistersEventListeners;

    private $processedRows = 0;
    private $failedRows = [];
    private $batchId;
    private $validMonthNames;

    public function __construct()
    {
        $this->batchId = 'FAL-' . date('y') . '-' . uniqid();
        $this->validMonthNames = [
            'january',
            'february',
            'march',
            'april',
            'may',
            'june',
            'july',
            'august',
            'september',
            'october',
            'november',
            'december'
        ];
    }

    public function prepareForValidation($data, $index)
    {
        $numericFields = [
            'orderquantity',
            'price',
            'totalamount',
            'cmpc',
            'totalcm',
            'consumption',
            'fabricqnty',
            'sewingbalance',
            'productionminutes',
            'productionbalance',
            'shippedqty',
            'excessshortshipmentqty',
            'color_quantity'
        ];

        foreach ($numericFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = $this->cleanNumericValue($data[$field]);
            }
        }

        return $data;
    }

    private function cleanNumericValue($value)
    {
        if (is_numeric($value)) return $value;
        return str_replace(['$', ',', ' '], '', (string)$value);
    }

    public function model(array $row)
{
    if ($this->isRowEmpty($row)) return null;

    DB::enableQueryLog();
    $normalizedRow = $this->normalizeKeys($row);

    try {
        $this->validateEssentialFields($normalizedRow);
        $buyer = $this->processBuyer($normalizedRow);
        
        // Map attributes without job_no
        $jobAttributes = $this->mapJobAttributes($normalizedRow, $buyer->id, $buyer->name, '');
        $matchingAttributes = $jobAttributes;
        unset($matchingAttributes['job_no']);

        // Find existing job by all attributes except job_no/id
        $existingJob = Job::where(function($query) use ($matchingAttributes) {
            foreach ($matchingAttributes as $key => $value) {
                if ($value === null) {
                    $query->whereNull($key);
                } else {
                    $query->where($key, $value);
                }
            }
        })->first();

        if ($existingJob) {
            // Update existing job
            $existingJob->update($matchingAttributes);
            $job = $existingJob;
        } else {
            // Create new job
            $jobNo = $this->generateJobNumber();
            $jobAttributes['job_no'] = $jobNo;
            $job = Job::create($jobAttributes);
        }

        $this->processShipment($normalizedRow, $job);
        $this->processSewingBalance($normalizedRow, $job);

        $this->processedRows++;
        return $job;
    } catch (\Exception $e) {
        $this->logError($e, $row, $normalizedRow);
        return null;
    } finally {
        DB::disableQueryLog();
    }
}

    public function onValidationError(ValidationException $e)
    {
        foreach ($e->failures() as $failure) {
            $rowNumber = $failure->row();
            $errors = implode(', ', $failure->errors());

            $this->failedRows[] = [
                'row' => $rowNumber,
                'error' => "Validation failed: " . $errors,
                'raw_data' => json_encode($failure->values()),
                'normalized_data' => ''
            ];

            Log::error("Validation Error: Row {$rowNumber} - {$errors}");
        }
    }

    public function rules(): array
    {
        return [
            'buyer' => 'required|string',
            // 'style' => 'nullable|string',
            // 'po' => 'nullable|string',
            // 'orderquantity' => 'nullable|numeric|min:0',
            // 'deliverydate' => 'nullable',
            // 'insdate' => 'nullable',
            // 'orderreceiveddate' => 'nullable',
            // 'exfactorydate' => 'nullable',
            // 'price' => 'nullable|numeric|min:0',
            // 'totalamount' => 'nullable|numeric|min:0',
            // 'cmpc' => 'nullable|numeric|min:0',
            // 'totalcm' => 'nullable|numeric|min:0',
            // 'consumption' => 'nullable|numeric|min:0',
            // 'fabricqnty' => 'nullable|numeric|min:0',
            // 'color_quantity' => 'nullable|numeric|min:0',
        ];
    }

    private function isRowEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if ($value !== null && trim((string)$value) !== '') {
                return false;
            }
        }
        return true;
    }

    private function normalizeKeys(array $row): array
    {
        $keyMap = [
            'buyer' => 'buyer',
            'style' => 'style',
            'po' => 'po',
            'department' => 'department',
            'item' => 'item',
            'destination' => 'destination',
            'color' => 'color',
            'size' => 'size',
            'color_quantity' => 'color_quantity',
            'orderquantity' => 'orderquantity',
            'sewingbalance' => 'sewingbalance',
            'shipmentplan' => 'shipmentplan',
            'ins_date' => 'insdate',
            'deliverydate' => 'deliverydate',
            'targetsmv' => 'targetsmv',
            'productionminutes' => 'productionminutes',
            'productionbalance' => 'productionbalance',
            'price' => 'price',
            'totalamount' => 'totalamount',
            'cm_pc' => 'cmpc',
            'totalcm' => 'totalcm',
            'consumption' => 'consumption',
            'fabricqnty' => 'fabricqnty',
            'fabrication' => 'fabrication',
            'orderreceiveddate' => 'orderreceiveddate',
            'aop' => 'aop',
            'print' => 'print',
            'embroidery' => 'embroidery',
            'wash' => 'wash',
            'print_wash' => 'print_wash',
            'remarks' => 'remarks',
            'shippedqty' => 'shippedqty',
            'exfactorydate' => 'exfactorydate',
            'shippedvalue' => 'shippedvalue',
            'excessshortshipmentqty' => 'excessshortshipmentqty',
            'excessshortshipmentvalue' => 'excessshortshipmentvalue',
            'deliverystatus' => 'deliverystatus',
            'buyer_hold_shipment' => 'buyer_hold_shipment',
            'buyer_hold_shipment_reason' => 'buyer_hold_shipment_reason',
            'buyer_hold_shipment_date' => 'buyer_hold_shipment_date',
            'buyer_cancel_shipment' => 'buyer_cancel_shipment',
            'buyer_cancel_shipment_reason' => 'buyer_cancel_shipment_reason',
            'buyer_cancel_shipment_date' => 'buyer_cancel_shipment_date',
            'order_close' => 'order_close',
            'order_close_reason' => 'order_close_reason',
            'order_close_date' => 'order_close_date',
            'order_close_by' => 'order_close_by',
        ];

        $normalizedRow = [];
        foreach ($row as $key => $value) {
            $cleanKey = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $key));
            if (isset($keyMap[$cleanKey])) {
                $normalizedRow[$keyMap[$cleanKey]] = $value;
            }
        }
        return $normalizedRow;
    }

    private function validateEssentialFields(array $row): void
    {
        if (empty($row['buyer'])) {
            throw new \Exception("Missing Buyer field");
        }
    }

    private function processBuyer(array $row): Buyer
    {
        $name = strtoupper(trim($row['buyer']));
        return Buyer::firstOrCreate(
            ['name' => $name],
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
        return $this->batchId . '-' . str_pad((string)($this->processedRows + count($this->failedRows) + 1), 4, '0', STR_PAD_LEFT);
    }

    private function mapJobAttributes(array $row, int $buyerId, string $buyerName, string $jobNo): array
    {
        $orderQuantity = $this->parseNumeric($row['orderquantity'] ?? 0);
        $orderQuantity = ceil($orderQuantity);

        return [
            'company_id' => 3,
            'division_id' => 2,
            'buyer_id' => $buyerId,
            'company_name' => 'FAL - Factory',
            'division_name' => 'Factory',
            'batch_id' => $this->batchId,
            'job_no' => $jobNo,
            'buyer' => $buyerName,
            'style' => $row['style'] ?? 'N/A',
            'po' => $row['po'] ?? 'N/A',
            'department' => $row['department'] ?? null,
            'item' => $row['item'] ?? null,
            'destination' => $row['destination'] ?? null,
            'color' => 'ALL',
            'size' => 'ALL',
            'color_quantity' => (int)$orderQuantity,
            'order_quantity' => (int)$orderQuantity,
            'production_plan' => $row['shipmentplan'] ?? null,
            'ins_date' => $this->parseDate($row['insdate'] ?? null),
            'delivery_date' => $this->parseDate($row['deliverydate'] ?? null),
            'target_smv' => $this->parseNumeric($row['targetsmv'] ?? 0),
            'production_minutes' => $this->parseNumeric($row['productionminutes'] ?? 0),
            'unit_price' => $this->parseNumeric($row['price'] ?? 0),
            'total_value' => $this->parseNumeric($row['totalamount'] ?? 0),
            'cm_pc' => $this->parseNumeric($row['cmpc'] ?? 0),
            'total_cm' => $this->parseNumeric($row['totalcm'] ?? 0),
            'consumption_dzn' => $this->parseNumeric($row['consumption'] ?? 0),
            'fabric_qnty' => $this->parseNumeric($row['fabricqnty'] ?? 0),
            'fabrication' => $row['fabrication'] ?? null,
            'order_received_date' => $this->parseDate($row['orderreceiveddate'] ?? null),
            // 'aop' => $row['aop'] ?? null,
            // 'print' => $row['print'] ?? null,
            // 'embroidery' => $row['embroidery'] ?? null,
            // 'wash' => $row['wash'] ?? null,
            // 'print_wash' => $row['print_wash'] ?? null,
            // 'remarks' => $row['remarks'] ?? null,
        ];
    }

    private function parseNumeric($value): ?float
    {
        if ($value === null || trim((string)$value) === '') {
            return 0;
        }
        $cleaned = str_replace(['$', ','], '', (string)$value);
        return is_numeric($cleaned) ? (float)$cleaned : 0;
    }

    private function parseDate($value): ?\DateTime
    {
        if (empty($value)) return null;

        try {
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
            }

            if (is_string($value)) {
                $formats = [
                    'Y-m-d',
                    'm/d/Y',
                    'd-m-Y',
                    'd/m/Y',
                    'Ymd',
                    'mdY',
                    'dmY'
                ];

                foreach ($formats as $format) {
                    $date = \DateTime::createFromFormat($format, $value);
                    if ($date !== false) {
                        return $date;
                    }
                }

                if ($parsed = strtotime($value)) {
                    return (new \DateTime())->setTimestamp($parsed);
                }
            }

            return new \DateTime($value);
        } catch (\Exception $e) {
            Log::warning("Date parsing failed: {$value}");
            return null;
        }
    }

    private function updateOrCreateJob(string $jobNo, array $attributes): Job
    {
        return Job::updateOrCreate(
            ['job_no' => $jobNo],
            $attributes
        );
    }

    private function processShipment(array $row, Job $job): void
    {
        if (empty($row['shippedqty']) && empty($row['exfactorydate']) && empty($row['deliverystatus'])) {
            return;
        }

        $shippedQty = ceil($this->parseNumeric($row['shippedqty'] ?? 0));
        $excessQty = ceil($this->parseNumeric($row['excessshortshipmentqty'] ?? 0));

        Shipment::updateOrCreate(
            ['job_id' => $job->id],
            [
                'job_no' => $job->job_no,
                'color' => 'ALL',
                'size' => 'ALL',
                'shipped_qty' => (int)$shippedQty,
                'ex_factory_date' => $this->parseDate($row['exfactorydate'] ?? null),
                'shipped_value' => $this->parseNumeric($row['shippedvalue'] ?? 0),
                'excess_short_shipment_qty' => (int)$excessQty,
                'excess_short_shipment_value' => $this->parseNumeric($row['excessshortshipmentvalue'] ?? 0),
                'delivery_status' => $row['deliverystatus'] ?? 'Pending',
                'buyer_hold_shipment' => $row['buyer_hold_shipment'] ?? null,
                'buyer_hold_shipment_reason' => $row['buyer_hold_shipment_reason'] ?? null,
                'buyer_hold_shipment_date' => $this->parseDate($row['buyer_hold_shipment_date'] ?? null),
                'buyer_cancel_shipment' => $row['buyer_cancel_shipment'] ?? null,
                'buyer_cancel_shipment_reason' => $row['buyer_cancel_shipment_reason'] ?? null,
                'buyer_cancel_shipment_date' => $this->parseDate($row['buyer_cancel_shipment_date'] ?? null),
                'order_close' => $row['order_close'] ?? null,
                'order_close_reason' => $row['order_close_reason'] ?? null,
                'order_close_date' => $this->parseDate($row['order_close_date'] ?? null),
                'order_close_by' => $row['order_close_by'] ?? null,
            ]
        );
    }


    private function processSewingBalance(array $row, Job $job): void
    {
        $sewingBalance = $this->parseNumeric($row['sewingbalance'] ?? null);
        $productionBalance = $this->parseNumeric($row['productionbalance'] ?? null);
        $shipmentPlan = $row['shipmentplan'] ?? null;

        if ($sewingBalance <= 0 || $productionBalance <= 0 || !$this->isValidShipmentPlan($shipmentPlan)) {
            return;
        }

        $sewingBalance = ceil($sewingBalance);

        //process for sewing plan accroding to shipment plan, and save 'job_id','job_no','production_plan','color','size','color_quantity'='sewingbalance'  if shipment plan is valid 
        $sewingPlanData = [
            'job_id' => $job->id,
            'job_no' => $job->job_no,
            'production_plan' => $shipmentPlan,
            'color' => 'ALL',
            'size' => 'ALL',
            'color_quantity' => $sewingBalance // This is the sewing balance quantity
        ];

        SewingPlan::updateOrCreate(
            ['job_id' => $job->id, 'production_plan' => $shipmentPlan],
            $sewingPlanData
        );
        // Update or create the sewing balance record

        SewingBalance::updateOrCreate(
            ['job_id' => $job->id],
            [
                'job_no' => $job->job_no,
                'sewing_plan_id' => $shipmentPlan->id ?? null,
                'color' => 'ALL',
                'size' => 'ALL',
                'sewing_balance' => (int)$sewingBalance,
                'production_plan' => $shipmentPlan,
                'production_min_balance' => $productionBalance
            ]
        );
    }

    private function isValidShipmentPlan(?string $value): bool
    {
        if (!$value) return false;

        $lowerValue = strtolower(trim($value));
        if (in_array($lowerValue, $this->validMonthNames)) {
            return true;
        }

        try {
            new \DateTime($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function logError(\Exception $e, array $rawRow, array $normalizedRow): void
    {
        $rowNumber = $this->processedRows + count($this->failedRows) + 1;
        $errorDetails = [
            'row' => $rowNumber,
            'error' => $e->getMessage(),
            'raw_data' => json_encode(array_slice($rawRow, 0, 5)),
            'normalized_data' => json_encode($normalizedRow),
        ];

        Log::error("Import Error: Row {$rowNumber} - {$e->getMessage()}", $errorDetails);
        $this->failedRows[] = $errorDetails;
    }

    public function getProcessedRows(): int
    {
        return $this->processedRows;
    }

    public function getFailedRows(): array
    {
        return $this->failedRows;
    }

    public function getBatchId(): string
    {
        return $this->batchId;
    }

    public function onError(\Throwable $e)
    {
        $rowNumber = $this->processedRows + count($this->failedRows) + 1;
        $this->failedRows[] = [
            'row' => $rowNumber,
            'error' => $e->getMessage(),
            'raw_data' => 'N/A',
            'normalized_data' => 'N/A'
        ];

        Log::error("Import Error (onError): Row {$rowNumber} - {$e->getMessage()}");
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
