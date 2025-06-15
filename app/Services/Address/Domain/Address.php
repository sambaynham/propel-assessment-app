<?php

declare(strict_types=1);

namespace App\Services\Address\Domain;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\QueryParameter;
use App\Services\Address\Application\ApiPlatformAddressProcessor;
use App\Services\Address\Application\ApiPlatformAddressProvider;


#[ApiResource(
    provider: ApiPlatformAddressProvider::class,
    processor: ApiPlatformAddressProcessor::class,
    rules: [
        'firstName' => [
            'string', 'required'
        ],
        'lastName' => [
            'string', 'required'
        ],
        'phone' => [
            'regex:/^(\S+)?((((\+44\s?([0–6]|[8–9])\d{3} | \(?0([0–6]|[8–9])\d{3}\)?)\s?\d{3}\s?(\d{2}|\d{3}))|((\+44\s?([0–6]|[8–9])\d{3}|\(?0([0–6]|[8–9])\d{3}\)?)\s?\d{3}\s?(\d{4}|\d{3}))|((\+44\s?([0–6]|[8–9])\d{1}|\(?0([0–6]|[8–9])\d{1}\)?)\s?\d{4}\s?(\d{4}|\d{3}))|((\+44\s?\d{4}|\(?0\d{4}\)?)\s?\d{3}\s?\d{3})|((\+44\s?\d{3}|\(?0\d{3}\)?)\s?\d{3}\s?\d{4})|((\+44\s?\d{2}|\(?0\d{2}\)?)\s?\d{4}\s?\d{4})))$/',
            'required',
        ],
        'email' => [
            'string', 'required'
        ]
    ]
)]
#[GetCollection(uriTemplate: '/', parameters: [
    'page' => new QueryParameter
])]
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

    public function jsonSerialize(): array
    {
        return [
            'first_name' => $this->getFirstName(),
            'last_name' => $this->getLastName(),
            'phone' => $this->getPhone(),
            'email' => $this->getEmail(),
            'id' => $this->getId()
        ];
    }

    /**
     * This isn't a particularly good solution, but the storage format required of the JSON doesn't allow for a non-data key.
     * The main issue with this would be the + character; it's valid in an e-mail address but would be stripped here.
     *
     * @return string
     */
    public function getId(): string
    {
        return base64_encode($this->getEmail());
    }
}
