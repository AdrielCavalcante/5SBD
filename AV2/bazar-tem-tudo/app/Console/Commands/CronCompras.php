<?php

namespace App\Console\Commands;

use App\Services\InventoryService;
use App\Services\IntegrationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CronCompras extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:cron-compras';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('CRONJOB - cron-compras executado - '. now());

        $this->info('Processando pedidos comprados...');
        $inventoryService = new InventoryService();
        $inventoryService->ProcessarPedidosComprados();
        
        Log::info('CRONJOB - cron-compras executado com sucesso - '. now());
        $this->info('CRONJOB - cron-compras executado com sucesso - '. now());
        return 0;
    }
}
