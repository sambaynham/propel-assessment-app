<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Address\Domain;

use App\Http\Requests\AddressPostRequest;
use App\Services\Address\Domain\Address;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class AddressTest extends TestCase
{

    /**
     * Valid test data provider. Provides all the information in the example as constructor arguments. This is my success condition.
     *
     * @return iterable
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
           'email' => 'jason.grimshaw@corrie.co.uk.'
       ];

       yield 'ken_barlow' => [
           'firstName' => 'Ken',
           'lastName' => 'Barlow',
           'phone' => '019134784929',
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

}