<?php

namespace App\Console\Commands;

use App\Services\Search\ElasticSearchService;
use Illuminate\Console\Command;

class CreateElasticIndexCommand extends Command
{
    public function __construct(
        private readonly ElasticSearchService $elasticSearchService,
    )
    {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:elastic:create-index';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates the default elastic index.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $this->elasticSearchService->createIndex();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }
        return self::SUCCESS;

    }
}
