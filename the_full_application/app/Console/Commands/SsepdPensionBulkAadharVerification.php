<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use App\Models\Pension\SsepdPension;

class SsepdPensionBulkAadharVerification extends Command
{
    protected $signature = 'ssepdpension:aadhar-verify {limit=200}';

    protected $description = 'Bulk Aadhaar verification for Ssepd Pensioners';

    public function handle()
    {
        $limit = (int) $this->argument('limit');

        $this->info("Starting Aadhaar verification for {$limit} records");

        $records = SsepdPension::whereNull('verified_aadhar')
            ->whereNull('verified_aadhar_remarks')
            ->whereNotNull('aadhar_number')
            ->whereNotNull('applicant_name')
            ->limit($limit)
            ->get();

        if ($records->isEmpty()) {
            $this->info('No pending Aadhaar records found');
            return Command::SUCCESS;
        }

        $processed = 0;

        foreach ($records as $pensioner) {

            $verified = 0;
            $remarks  = null;

            try {
                $response = Http::withOptions([
                    'verify' => false,
                    'timeout' => 120,
                    'connect_timeout' => 20,
                    'curl' => [
                        \CURLOPT_SSLVERSION   => \CURL_SSLVERSION_TLSv1_2,
                        \CURLOPT_HTTP_VERSION => \CURL_HTTP_VERSION_1_1,
                    ],
                ])
                ->withHeaders([
                    'Accept'     => 'application/json',
                    'User-Agent' => 'PostmanRuntime/7.36.0',
                ])
                ->asForm()
                ->post('https://ssepd.gov.in:8443/swp/api/nfbs/requestToUid', [
                    'aadhaar_no' => trim($pensioner->aadhar_number),
                    'name'       => trim($pensioner->applicant_name),
                ]);

                $remarks = trim($response->body());

                if (
                    $response->successful() &&
                    str_contains(strtolower($remarks), 'verify successfully')
                ) {
                    $verified = 1;
                }

            } catch (\Throwable $e) {
                $verified = 0;
                $remarks  = 'Exception: ' . $e->getMessage();
            }

            $pensioner->update([
                'verified_aadhar'         => $verified,
                'verified_aadhar_remarks' => $remarks,
            ]);

            $processed++;
        }

        $this->info("Completed. Processed {$processed} records.");

        return Command::SUCCESS;
    }
}
