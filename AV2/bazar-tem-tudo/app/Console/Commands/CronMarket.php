<?php

namespace App\Console\Commands;

use App\Services\InventoryService;
use App\Services\IntegrationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CronMarket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:cron-market';

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
        Log::info('CRONJOB - cron-market executado - '. now());
        $this->info('Importando dados da carga...');
        $integrationService = new IntegrationService();
        $integrationService->importarDadosCarga();
        
        $this->info('Processando pedidos...');
        $inventoryService = new InventoryService();
        $inventoryService->processarPedidos();
        
        Log::info('CRONJOB - cron-market executado com sucesso - '. now());
        $this->info('CRONJOB - cron-market executado com sucesso - '. now());
        return 0;
    }
}
