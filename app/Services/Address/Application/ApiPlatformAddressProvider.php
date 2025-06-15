<?php

declare(strict_types=1);

namespace App\Services\Address\Application;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Services\Address\Domain\Address;
use App\Services\Address\Service\AddressServiceInterface;


/**
 * @implements ProviderInterface<Address>
 */
readonly class ApiPlatformAddressProvider implements ProviderInterface
{

    public function __construct(
        private AddressServiceInterface $addressService
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface) {
            return $this->addressService->findAll();
        }

        return is_string($uriVariables['id']) ? $this->addressService->loadById($uriVariables['id']) : null;

    }
}
