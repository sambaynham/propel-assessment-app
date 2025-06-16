<?php

declare(strict_types=1);

namespace App\Services\Address\Domain;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\QueryParameter;
use App\Services\Address\Application\ApiPlatformAddressProcessor;
use App\Services\Address\Application\ApiPlatformAddressProvider;
use App\Services\Address\Domain\Exceptions\MalformedAddressException;


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

    public const string ACCEPTABLE_CHARACTERS_PATTERN = "/[^A-Za-z0-9 \- ']/";

    public const string UK_PHONE_REGEX = '/^(\S+)?((((\+44\s?([0–6]|[8–9])\d{3} | \(?0([0–6]|[8–9])\d{3}\)?)\s?\d{3}\s?(\d{2}|\d{3}))|((\+44\s?([0–6]|[8–9])\d{3}|\(?0([0–6]|[8–9])\d{3}\)?)\s?\d{3}\s?(\d{4}|\d{3}))|((\+44\s?([0–6]|[8–9])\d{1}|\(?0([0–6]|[8–9])\d{1}\)?)\s?\d{4}\s?(\d{4}|\d{3}))|((\+44\s?\d{4}|\(?0\d{4}\)?)\s?\d{3}\s?\d{3})|((\+44\s?\d{3}|\(?0\d{3}\)?)\s?\d{3}\s?\d{4})|((\+44\s?\d{2}|\(?0\d{2}\)?)\s?\d{4}\s?\d{4})))$/';
    /**
     * @throws MalformedAddressException
     */
    public function __construct(
        private string $firstName,
        private string $lastName,
        private string $phone,
        private string $email,
    ) {
        $this->guard();
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

    /**
     * @return void
     * @throws MalformedAddressException
     */
    private function guard(): void {
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new MalformedAddressException(sprintf("%s is not a valid email address.", $this->getEmail()));
        }
        if (preg_match(self::ACCEPTABLE_CHARACTERS_PATTERN, $this->firstName)) {
            throw new MalformedAddressException(sprintf("The First name '%s' contains invalid characters.", $this->getFirstName()));
        }
        if (preg_match(self::ACCEPTABLE_CHARACTERS_PATTERN, $this->lastName)) {
            throw new MalformedAddressException(sprintf("The Last name '%s' contains invalid characters.", $this->getLastName()));
        }
        if (preg_match(self::ACCEPTABLE_CHARACTERS_PATTERN, $this->lastName)) {
            throw new MalformedAddressException(sprintf("The Last name '%s' contains invalid characters.", $this->getLastName()));
        }
        if (!preg_match(self::UK_PHONE_REGEX, $this->getPhone())) {
            throw new MalformedAddressException(sprintf("The phone number '%s' is not a valid U.K phone number", $this->getPhone()));
        }
    }
}
