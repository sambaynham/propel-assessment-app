<?php

namespace App\Console\Commands;

use App\Services\Address\Infrastructure\AddressRepositoryInterface;
use App\Services\Search\ElasticSearchService;
use App\Services\Search\Exceptions\ElasticaClientException;
use App\Services\Search\Exceptions\ElasticaServerException;
use Illuminate\Console\Command;

class PopulateElasticCommand extends Command
{
    public function __construct(
        private readonly ElasticSearchService $elasticSearchService,
        private readonly AddressRepositoryInterface $addressRepository
    ) {
        parent::__construct();
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:elastic:populate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populates Elastic';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $addresses = $this->addressRepository->findAll();
        foreach ($addresses as $address) {
            $this->info(sprintf("Populating %s", $address->getEmail()));
            try {
                $this->elasticSearchService->populateIndex($address->jsonSerialize());
            } catch (ElasticaClientException|ElasticaServerException $e) {
                $this->error($e->getMessage());
                return self::FAILURE;
            }
            $this->info(sprintf("Done populating %s", $address->getEmail()));
        }

        return self::SUCCESS;
    }
}
