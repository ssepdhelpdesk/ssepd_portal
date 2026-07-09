<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Website_Controller\WebsiteNsapDumpCOntroller;

class UpdateTheAadharNBankMobileDataUsingNsapApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-the-aadhar-n-bank-mobile-data-using-nsap-api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Aadhaar, Bank Account, IFSC and Mobile Number from NSAP API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('===================================================');
        $this->info('NSAP Synchronization Started');
        $this->info('Started At : ' . now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'));
        $this->info('===================================================');

        Log::info('========== NSAP Artisan Command Started ==========');

        try {

            $controller = new WebsiteNsapDumpCOntroller();

            $response = $controller->update_the_data_using_nsap_api();

            $responseData = json_decode($response->getContent(), true);

            $this->info(json_encode($responseData, JSON_PRETTY_PRINT));

            Log::info('========== NSAP Artisan Command Completed ==========', [
                'response' => $responseData
            ]);

            return Command::SUCCESS;

        } catch (\Throwable $e) {

            Log::error('========== NSAP Artisan Command Failed ==========', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ]);

            $this->error($e->getMessage());

            return Command::FAILURE;
        }
    }
}