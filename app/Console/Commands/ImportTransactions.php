<?php

namespace App\Console\Commands;

use App\Models\PdCode;
use App\Models\Project;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ImportTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:transactions {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import transactions from a CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("File not found: $filePath");
            return 1;
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            $this->error("Unable to open file: $filePath");
            return 1;
        }

        // Read first line to detect delimiter
        $firstLine = fgets($handle);
        rewind($handle);
        $delimiter = $this->detectDelimiter($firstLine);

        $imported = 0;
        $skipped = 0;
        $warnings = [];
        $importBatch = basename($filePath);

        // Map special strings to PD codes
        $specialStrings = [
            'Other Expenses' => 'OTH_EXP',
            'Vehicle Fuel' => 'VEH_FUEL',
            'Vehicle Repair' => 'VEH_REP',
            'Travel' => 'TRAVEL',
        ];

        // Area code inference map
        $areaPrefixes = [
            '10S' => '10',
            '19S' => '19',
            '20G' => '20',
            '60A' => '60A-60C',
            '60B' => '60A-60C',
            '60C' => '60A-60C',
            '60D' => '60D',
            '60E' => '60E',
            '60F' => '60F',
            '30B' => '60A-60C',
            '30C' => '60A-60C',
            '30D' => '60D',
            '30E' => '60E',
        ];

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            try {
                // Parse columns: Date, PD Code, Area Code, AC Code MH, Project Nr, Item Description, Amount
                // Some rows are missing AC Code MH (column 4), resulting in 6 columns instead of 7
                if (count($row) < 6) {
                    continue; // Skip rows with fewer than 6 columns
                }

                $dateStr = trim($row[0]);
                $rawPdCode = trim($row[1]);
                $rawAreaCode = trim($row[2]);

                // Normalize: if 6 columns, the AC Code MH is missing; if 7+ columns, it's present
                if (count($row) == 6) {
                    // Missing AC Code MH - use default empty value
                    $rawAcCodeMh = "";
                    $rawProjectNr = trim($row[3]);
                    $itemDescription = trim($row[4]);
                    $amountStr = trim($row[5]);
                } else {
                    // Normal case with AC Code MH
                    $rawAcCodeMh = trim($row[3]);
                    $rawProjectNr = trim($row[4]);
                    $itemDescription = trim($row[5]);
                    $amountStr = trim($row[6]);
                }

                // Parse date
                $transactionDate = $this->parseDate($dateStr);
                if (!$transactionDate) {
                    $skipped++;
                    continue;
                }

                // Parse amount
                $amount = $this->parseAmount($amountStr);
                if ($amount === null) {
                    $skipped++;
                    continue;
                }

                // Map special strings to PD codes
                $pdCodeToResolve = $rawPdCode;
                foreach ($specialStrings as $specialStr => $specialCode) {
                    if (stripos($rawPdCode, $specialStr) !== false) {
                        $pdCodeToResolve = $specialCode;
                        break;
                    }
                }

                // Resolve PD code
                $pdCode = PdCode::where('code', $pdCodeToResolve)->first();
                if (!$pdCode) {
                    $skipped++;
                    continue;
                }

                // Infer area code if blank
                $areaCodeToUse = $rawAreaCode;
                if (empty($areaCodeToUse) && $pdCode) {
                    foreach ($areaPrefixes as $prefix => $area) {
                        if (strpos($pdCode->code, $prefix) === 0) {
                            $areaCodeToUse = $area;
                            break;
                        }
                    }
                }

                // Skip excluded codes
                if ($pdCode->category === 'excluded') {
                    $skipped++;
                    continue;
                }

                // Resolve project - try by numeric ID, then by name, then create new
                $project = null;
                if (!empty($rawProjectNr)) {
                    // Try to match by numeric project_nr
                    $project = Project::where('project_nr', $rawProjectNr)->first();

                    // If not found, try to match by name
                    if (!$project) {
                        $project = Project::where('name', $rawProjectNr)->first();
                    }

                    // If still not found, create a new project
                    if (!$project) {
                        try {
                            $project = Project::create([
                                'name' => substr($rawProjectNr, 0, 255), // Limit to 255 chars
                                'project_type' => 'historical',
                                'region_id' => 1, // Default region
                            ]);
                        } catch (\Exception $e) {
                            $skipped++;
                            continue;
                        }
                    }
                }

                // Upsert transaction
                Transaction::updateOrCreate(
                    [
                        'raw_pd_code' => $rawPdCode,
                        'raw_project_nr' => $rawProjectNr,
                        'item_description' => $itemDescription,
                        'amount' => $amount,
                    ],
                    [
                        'transaction_date' => $transactionDate,
                        'pd_code_id' => $pdCode->id,
                        'raw_area_code' => $rawAreaCode,
                        'raw_ac_code_mh' => $rawAcCodeMh,
                        'project_id' => $project?->id,
                        'import_batch' => $importBatch,
                    ]
                );

                $imported++;
            } catch (\Exception $e) {
                $warnings[] = "Error processing row: " . $e->getMessage();
                $skipped++;
            }
        }

        fclose($handle);

        $this->info("Import complete:");
        $this->info("  Imported: $imported");
        $this->info("  Skipped: $skipped");

        if (count($warnings) > 0) {
            $this->warn("\nWarnings (" . count($warnings) . "):");
            foreach (array_slice($warnings, 0, 20) as $warning) {
                $this->warn("  - $warning");
            }
            if (count($warnings) > 20) {
                $this->warn("  ... and " . (count($warnings) - 20) . " more");
            }
        }

        return 0;
    }

    /**
     * Detect delimiter (tab or comma)
     */
    private function detectDelimiter(string $line): string
    {
        $tabCount = substr_count($line, "\t");
        $commaCount = substr_count($line, ",");
        return $tabCount > $commaCount ? "\t" : ",";
    }

    /**
     * Parse date in multiple formats
     */
    private function parseDate(string $dateStr): ?Carbon
    {
        $dateStr = trim($dateStr);
        if (empty($dateStr)) {
            return null;
        }

        $parts = preg_split('/[\/\-\.]/', $dateStr);
        if (count($parts) !== 3) {
            return null;
        }

        $p1 = (int) $parts[0];
        $p2 = (int) $parts[1];
        $p3 = (int) $parts[2];

        try {
            // Detect format based on values
            // If first segment > 12, it's DD/MM/YYYY
            if ($p1 > 12) {
                return Carbon::createFromFormat('d/m/Y', $dateStr);
            }
            // If second segment > 12, it's M/DD/YYYY (US format)
            elseif ($p2 > 12) {
                return Carbon::createFromFormat('m/d/Y', $dateStr);
            }
            // If both ≤ 12, assume DD/MM/YYYY (stated primary format)
            else {
                return Carbon::createFromFormat('d/m/Y', $dateStr);
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Parse amount (strip commas, convert to float)
     */
    private function parseAmount(string $amountStr): ?float
    {
        $amountStr = trim($amountStr);
        if (empty($amountStr)) {
            return null;
        }

        // Remove commas and other formatting
        $amountStr = str_replace(',', '', $amountStr);
        $amount = (float) $amountStr;

        return is_nan($amount) ? null : $amount;
    }
}
