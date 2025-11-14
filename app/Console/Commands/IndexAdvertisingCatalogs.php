<?php

namespace App\Console\Commands;

use App\Facades\AdvertisingProductHub;
use Illuminate\Console\Command;

class IndexAdvertisingCatalogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Accepts an optional comma-separated list of vendors as an argument.
     * If omitted, it will index: probo, drukwerkdeal, toppoint
     *
     * @var string
     */
    protected $signature = 'indexing:advertising-catalogs {vendors?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index advertising product catalogs for one or more vendors (comma-separated). Defaults to probo,drukwerkdeal,toppoint';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $vendorsArg = $this->argument('vendors');

        if (empty($vendorsArg)) {
            $vendors = ['probo', 'drukwerkdeal', 'toppoint'];
        } else {
            // split comma-separated list and trim each vendor name, ignore empty values
            $vendors = array_filter(array_map('trim', explode(',', $vendorsArg)));
        }

        foreach ($vendors as $vendor) {
            $this->info("Indexing products for vendor: {$vendor}");

            $start = microtime(true);
            AdvertisingProductHub::indexAllProducts($vendor);
            $elapsedMs = (int) round((microtime(true) - $start) * 1000);

            $this->info("Indexed products for {$vendor} in {$elapsedMs} milliseconds!");
        }

        $this->info('Indexing complete.');
    }
}
