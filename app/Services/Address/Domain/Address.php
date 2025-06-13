<?php

declare(strict_types=1);

namespace App\Services\Address\Domain;

use App\Http\Requests\AddressPostRequest;

class Address implements AddressInterface {
    public function __construct(
        private string $firstName,
        private string $lastName,
        private string $phone,
        private string $email,
    ) {

    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'first_name' => $this->getFirstName(),
            'last_name' => $this->getLastName(),
            'phone' => $this->getPhone(),
            'email' => $this->getEmail(),
        ];
    }

    /**
     * This isn't a particularly good solution, but the storage format required of the JSON doesn't allow for a non-data key.
     * The main issue with this would be the + character; it's valid in an e-mail address but would be stripped here.
     *
     * @return string
     */
    public function getUrlSafeEmail(): string
    {
        return strtolower(urlencode($this->getEmail()));
    }
}
