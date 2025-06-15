<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Search\ElasticSearchService;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\ServiceProvider;
use Elastic\Elasticsearch\ClientInterface as ElasticSearchClientInterface;

class ElasticSearchProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {

        $this->app->bind(ElasticSearchClientInterface::class, function ($app) {
            return ClientBuilder::create()
                ->setHosts(
                    [
                        'es01:9200'
                    ]
                )->setBasicAuthentication(
                    config('elastic.user'),
                    config('elastic.password')
                )
                ->build();
        });

        $this->app->bind(ElasticSearchService::class, function ($app) {
            return new ElasticSearchService($app->get(ElasticSearchClientInterface::class));
        });

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
