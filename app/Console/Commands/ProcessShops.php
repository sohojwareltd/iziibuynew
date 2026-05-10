<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Shop;
use App\Payment\Surfboard\SurfboardMarchant;
use App\Payment\Surfboard\SurfboardTerminal;
use Illuminate\Support\Facades\Log;

class ProcessShops extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shops:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process shops with surfboard payment method and contract status 0';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("Processing shops...");

        $shops = Shop::where('paymentMethod', 'surfboard')
            ->where('contract_status', 0)
            ->whereDoesntHave('metas', function ($query) {
                $query->where('column_name', 'surfboard_terminalId');
            })
            ->get();

        foreach ($shops as $shop) {
            try {
                // Check merchant status
                $application = (new SurfboardMarchant($shop))->statusCheck()->json();
                $applicationStatus = $application['data']['applicationStatus'] ?? null;

                // Skip if application status is not successful
                if ($application['status'] !== "SUCCESS" || $applicationStatus !== "MERCHANT_CREATED") {
                    $shop->createMetas([
                        "surfboard_applicationStatus" => $applicationStatus,
                    ]);
                    continue;
                }

                // If merchant and store IDs exist
                if ($shop->surfboard_merchantId && $shop->surfboard_storeId) {
                    // Skip if terminal ID already exists
                    if ($shop->surfboard_terminalId) {
                        continue;
                    }

                    // Create terminal
                    $terminal = (new SurfboardTerminal($shop))->createTerminal()->json();
                    $terminalId = $terminal['data']['terminalId'] ?? null;

                    if ($terminalId) {
                        $shop->update([
                            'contract_status' => 1
                        ]);
                        $shop->createMeta('surfboard_terminalId', $terminalId);
                    } else {
                        Log::warning("Failed to create terminal for Shop ID {$shop->id}", [
                            'response' => $terminal,
                        ]);
                    }
                } else {
                    // Save merchant and store details
                    $shop->createMetas([
                        "surfboard_applicationStatus" => $applicationStatus,
                        "surfboard_storeId" => $application['data']['storeId'] ?? null,
                        "surfboard_merchantId" => $application['data']['merchantId'] ?? null,
                    ]);
                }
            } catch (\Exception $e) {
                // Log exceptions to debug errors
                Log::error("Error processing Shop ID {$shop->id}", [
                    'error' => $e->getMessage(),
                    'stack' => $e->getTraceAsString(),
                ]);
                continue;
            }
        }

        $this->info("Shop processing completed!");
        return 0;
    }
}
