<?php

declare(strict_types=1);

namespace App\Services\Address\Application;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Services\Address\Domain\Address;
use App\Services\Address\Infrastructure\AddressRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @implements ProviderInterface<Address>
 */
readonly class ApiPlatformAddressProvider implements ProviderInterface
{

    public function __construct(
        private AddressRepositoryInterface $addressRepository,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface) {
            return $this->addressRepository->findAll();
        }

        return is_string($uriVariables['id']) ? $this->addressRepository->loadById($uriVariables['id']) : null;

    }
}
