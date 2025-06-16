<?php

declare(strict_types=1);

namespace App\Services\Address\Service;

use App\Services\Address\Domain\Address;
use App\Services\Address\Infrastructure\AddressRepositoryInterface;
use App\Services\Search\ElasticSearchService;
use App\Services\Search\Exceptions\ElasticaClientException;
use App\Services\Search\Exceptions\ElasticaServerException;

readonly class AddressService implements AddressServiceInterface
{
    public function __construct(
        private AddressRepositoryInterface $addressRepository,
        private ElasticSearchService $elasticSearchService
    ) {}


    public function save(Address $address): void
    {
        $this->addressRepository->persist($address);
        try {
            $this->elasticSearchService->populateIndex($address->jsonSerialize());
        } catch (ElasticaClientException|ElasticaServerException $e) {
            abort(500, $e->getMessage());
        }

    }

    public function delete(Address $address): void
    {
        $this->addressRepository->delete($address);
        try {
            $this->elasticSearchService->delete($address->getEmail());
        } catch (ElasticaClientException|ElasticaServerException $e) {
            abort(500, $e->getMessage());
        }
    }

    public function loadById(string $identifier): ?Address
    {
        return $this->addressRepository->loadById($identifier);
    }

    public function loadByEmail(string $email): ?Address
    {
        return $this->addressRepository->loadByEmail($email);
    }

    public function findAll(): array
    {
        return $this->addressRepository->findAll();
    }
}