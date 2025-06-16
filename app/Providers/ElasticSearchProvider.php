<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Search\ElasticSearchService;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\ClientInterface as ElasticSearchClientInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider;

class ElasticSearchProvider extends ServiceProvider
{
    /**
     * Register services.
     * @throws BindingResolutionException
     */
    public function register(): void
    {

        $elasticUser = config('elastic.user');
        $elasticPassword = config('elastic.password');
        if (!is_string($elasticUser) || !is_string($elasticPassword)) {
            throw new BindingResolutionException('Elastic search requires username and password');
        }
        $this->app->bind(ElasticSearchClientInterface::class, function ($app) use ($elasticUser, $elasticPassword) {
            return ClientBuilder::create()
                ->setHosts(
                    [
                        'es01:9200'
                    ]
                )->setBasicAuthentication(
                    $elasticUser,
                    $elasticPassword
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
