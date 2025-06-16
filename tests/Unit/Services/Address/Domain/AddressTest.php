<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Address\Domain;

use App\Http\Requests\AddressPostRequest;
use App\Services\Address\Domain\Address;
use App\Services\Address\Domain\Exceptions\MalformedAddressException;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class AddressTest extends TestCase
{

    /**
     * Valid test data provider. Provides all the information in the example as constructor arguments. This is my success condition.
     *
     * @return iterable<string, array>
     */
    public static function provideValidAddressData(): iterable {
       yield 'david_platt' => [
           'firstName' => 'David',
           'lastName' => 'Platt',
           'phone' => '01913478234',
           'email' => 'david.platt@corrie.co.uk'
       ];

       yield 'jason_grimshaw' => [
           'firstName' => 'Jason',
           'lastName' => 'Grimshaw',
           'phone' => '01913478123',
           'email' => 'jason.grimshaw@corrie.co.uk'
       ];

       yield 'ken_barlow' => [
           'firstName' => 'Ken',
           'lastName' => 'Barlow',
           'phone' => '01913478129',
           'email' => 'ken.barlow@corrie.co.uk'
       ];

       yield 'rita_sullivan' => [
           'firstName' => 'Rita',
           'lastName' => 'Sullivan',
           'phone' => '01913478555',
           'email' => 'rita.sullivan@corrie.co.uk'
       ];

       yield 'steve_mcdonald' => [
           'firstName' => 'Steve',
           'lastName' => 'McDonald',
           'phone' => '01913478555',
           'email' => 'steve.mcdonald@corrie.co.uk'
       ];
    }

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $phone
     * @param string $email
     *
     * @return void
     * @throws MalformedAddressException
     */
    #[DataProvider('provideValidAddressData')]
    public function testFullConstruct(
        string $firstName,
        string $lastName,
        string $phone,
        string $email
    ): void {
        $address = new Address(
            $firstName,
            $lastName,
            $phone,
            $email
        );

        self::assertEquals($firstName, $address->getFirstName());
        self::assertEquals($lastName, $address->getLastName());
        self::assertEquals($phone, $address->getPhone());
        self::assertEquals($email, $address->getEmail());

    }

    public function testJsonSerialize(): void {
        $address = new Address('Malcolm', 'Reynolds', '01234 567891', 'mal@serenity.ship');
        self::assertEquals([
            'first_name' =>  'Malcolm',
            'last_name' =>  'Reynolds',
            'phone' =>  '01234 567891',
            'email' => 'mal@serenity.ship',
            'id' => base64_encode('mal@serenity.ship'),

        ], $address->jsonSerialize());
    }

    public function testGetId(): void {
        $address = new Address('Malcolm', 'Reynolds', '01234 567891', 'mal@serenity.ship');
        self::assertEquals(base64_encode($address->getEmail()),$address->getId());
    }

    /**
     * @return iterable<string, array>
     */
    public static function provideInvalidAddressData(): iterable {
        yield 'malformed_email_address' => [
            'invalidAddressData' => [
                'firstName' => 'David',
                'lastName' => 'Platt',
                'phone' => '01913478234',
                'email' => 'david.platt@corrie.co.uk.'
            ],
            'expectedExceptionMessage' => "david.platt@corrie.co.uk. is not a valid email address."
        ];

        yield 'malformed_first_name' => [
            'invalidAddressData' => [
                'firstName' => 'David; DROP TABLE users.*',
                'lastName' => 'Platt',
                'phone' => '01913478234',
                'email' => 'david.platt@corrie.co.uk'
            ],
            'expectedExceptionMessage' => "The First name 'David; DROP TABLE users.*' contains invalid characters."
        ];

        yield 'malformed_last_name' => [
            'invalidAddressData' => [
                'firstName' => 'David',
                'lastName' => '<a href=\"dropship.cn\">Click here for great deals on shoes!</a>',
                'phone' => '01913478234',
                'email' => 'david.platt@corrie.co.uk'
            ],
            'expectedExceptionMessage' => 'The Last name \'<a href=\"dropship.cn\">Click here for great deals on shoes!</a>\' contains invalid characters.'
        ];

        yield 'malformed_phone' => [
            'invalidAddressData' => [
                'firstName' => 'David',
                'lastName' => 'Platt',
                'phone' => '0121345678940111244751',
                'email' => 'david.platt@corrie.co.uk'
            ],
            'expectedExceptionMessage' => 'The phone number \'0121345678940111244751\' is not a valid U.K phone number'
        ];
    }
    /**
     * @param array<string, string> $invalidAddressData
     * @param string $expectedExceptionMessage
     * @return void
     */

    #[DataProvider('provideInvalidAddressData')]
    public function testGuard(array $invalidAddressData, string $expectedExceptionMessage): void {
        $this->expectException(MalformedAddressException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        new Address(
            ...$invalidAddressData
        );
    }

}