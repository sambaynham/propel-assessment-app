<?php

declare(strict_types=1);

namespace App\Services\Address\Application;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Services\Address\Domain\Address;
use App\Services\Address\Service\AddressServiceInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * @implements ProcessorInterface<Address, Address|void>
 */
class ApiPlatformAddressProcessor implements ProcessorInterface {

    public function __construct(private AddressServiceInterface $addressService) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if ($operation instanceof DeleteOperationInterface) {
            $this->addressService->delete($data);
        } else {
            if ($operation instanceof Post) {
                $existingAddress = $this->addressService->loadByEmail($data->getEmail());
                if (null !== $existingAddress) {
                    throw new UnprocessableEntityHttpException("An address with this email address already exists.");
                }
            }
            $this->addressService->save($data);
        }

        return $data;
    }
}