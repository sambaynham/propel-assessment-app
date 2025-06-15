<?php

declare(strict_types=1);

namespace App\Services\Address\Infrastructure;

use App\Services\Address\Domain\Address;

interface AddressRepositoryInterface
{
    public function persist(Address $address): void;

    public function delete(Address $address): void;

    public function loadById(string $identifier): ?Address;

    public function loadByEmail(string $email): ?Address;

    /**
     * @return array<Address>
     */
    public function findAll(): array;
}