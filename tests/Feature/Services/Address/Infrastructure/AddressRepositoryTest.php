<?php

declare(strict_types=1);

namespace Tests\Feature\Services\Address\Infrastructure;

use App\Services\Address\Domain\Address;
use App\Services\Address\Infrastructure\AddressRepository;
use App\Services\Address\Infrastructure\Exceptions\MappingException;
use App\Services\Address\Infrastructure\Exceptions\ReadException;
use App\Services\Address\Infrastructure\Exceptions\WriteException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\TestCase;
use PHPUnit\Framework\MockObject\Exception;

class AddressRepositoryTest extends TestCase
{

    private const array TEST_ADDRESSES = [
        [
            'firstName' => 'David',
            'lastName' => 'Platt',
            'phone' => '01913478234',
            'email' => 'david.platt@corrie.co.uk'
        ],
        [
            'firstName' => 'Jason',
            'lastName' => 'Grimshaw',
            'phone' => '01913478123',
            'email' => 'jason.grimshaw@corrie.co.uk.'
        ],
        [
            'firstName' => 'Ken',
            'lastName' => 'Barlow',
            'phone' => '019134784929',
            'email' => 'ken.barlow@corrie.co.uk'
        ],
        [
            'firstName' => 'Rita',
            'lastName' => 'Sullivan',
            'phone' => '01913478555',
            'email' => 'rita.sullivan@corrie.co.uk'
        ],
        [
            'firstName' => 'Steve',
            'lastName' => 'McDonald',
            'phone' => '01913478555',
            'email' => 'steve.mcdonald@corrie.co.uk'
        ]
    ];

    /**
     * @throws Exception
     */
    private function generateMockFileSystem(int $putCount = 0, ? string $getResults = null): Filesystem {
        $mockFileSystem = $this->createMock(Filesystem::class);
        $mockFileSystem->expects($this->exactly($putCount))->method('put')->willReturn(true);
        $mockFileSystem->method('get')->willReturn($getResults);
        return $mockFileSystem;
    }

    /**
     * @throws ReadException
     * @throws Exception|MappingException|WriteException
     */
    public function testPersist(): void {
        $firstName = "Firstname";
        $lastName = "Lastname";
        $emailAddress = "test@test.local";
        $phoneNumber = "01234 567891";

        $address = new Address($firstName, $lastName, $phoneNumber, $emailAddress);

        $mockFileSystem = $this->generateMockFileSystem(2, '[{"first_name":"Firstname","last_name":"Lastname","phone":"01234 567891","email":"test@test.local"}]');
        $repository = new AddressRepository($mockFileSystem);
        $repository->persist($address);

        $result = $repository->loadById($address->getId());
        self::assertEquals($result, $address);
    }



    /**
     * @throws WriteException
     * @throws ReadException
     * @throws MappingException|Exception
     */
    public function testDelete(): void {
        $firstName = "Firstname";
        $lastName = "Lastname";
        $emailAddress = "test@test.local";
        $phoneNumber = "01234 567891";
        $address = new Address($firstName, $lastName, $phoneNumber, $emailAddress);

        $mockFileSystem = $this->generateMockFileSystem(3, '[{"first_name":"Firstname","last_name":"Lastname","phone":"01234 567891","email":"test@test.local"}]');
        $repository = new AddressRepository($mockFileSystem);
        $repository->persist($address);
        $result = $repository->loadById($address->getId());
        self::assertNotNull($result);
        $repository->delete($result);
        self::assertNull($repository->loadById($address->getId()));
    }

    /**
     * @throws ReadException
     * @throws WriteException
     * @throws MappingException|Exception
     */
    public function testFindAll(): void
    {
        $mockFileSystem = $this->generateMockFileSystem(6, '[]');
        $repository = new AddressRepository($mockFileSystem);

        $addressCollection = array_map(function ($address) {
            return new Address($address['firstName'], $address['lastName'], $address['phone'], $address['email']);
        }, self::TEST_ADDRESSES);

        foreach ($addressCollection as $address) {
            $repository->persist($address);
        }

        //Check that alphabetical sorting works.
        usort($addressCollection, function($a, $b) {
            return strcasecmp($a->getLastName(), $b->getLastName());
        });

        self::assertEquals($addressCollection, $repository->findAll());
    }

    /**
     * @throws ReadException
     * @throws Exception
     * @throws MappingException
     * @throws WriteException
     */
    public function testFindByEmail(): void {
        $mockFileSystem = $this->generateMockFileSystem(6, '[]');
        $repository = new AddressRepository($mockFileSystem);
        $addressCollection = array_map(function ($address) {
            return new Address($address['firstName'], $address['lastName'], $address['phone'], $address['email']);
        }, self::TEST_ADDRESSES);
        foreach ($addressCollection as $address) {
            $repository->persist($address);
        }

        self::assertNotNull($repository->loadByEmail('david.platt@corrie.co.uk'));
        self::assertNull($repository->loadByEmail('some.nonexistent.email@test.dev'));
    }

}