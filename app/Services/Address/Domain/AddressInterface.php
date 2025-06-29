<?php

declare(strict_types=1);

namespace App\Services\Address\Domain;

use App\Http\Requests\AddressPostRequest;

interface AddressInterface extends \JsonSerializable
{
    public function getId(): string;

    public function getFirstName(): string;

    public function getLastName(): string;

    public function getPhone(): string;

    public function getEmail(): string;

    public function setFirstName(string $firstName): void;

    public function setLastName(string $lastName): void;

    public function setPhone(string $phone): void;

    public function setEmail(string $email): void;
}