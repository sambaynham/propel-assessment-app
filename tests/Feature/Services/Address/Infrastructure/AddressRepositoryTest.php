<?php

declare(strict_types=1);

namespace tests\Feature\Services\Address\Infrastructure;

use App\Services\Address\Domain\Address;
use App\Services\Address\Infrastructure\AddressRepository;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\TestCase;

class AddressRepositoryTest extends TestCase
{

    private function generateMockFileSystem(int $putCount = 0, ? string $getResults = null): Filesystem {
        $mockFileSystem = $this->createMock(Filesystem::class);
        $mockFileSystem->expects($this->exactly($putCount))->method('put')->willReturn(true);
        $mockFileSystem->method('get')->willReturn($getResults);
        return $mockFileSystem;
    }

    public function testPersist(): void {
        $firstName = "Firstname";
        $lastName = "Lastname";
        $emailAddress = "test@test.local";
        $phoneNumber = "01234 567891";

        $address = new Address($firstName, $lastName, $phoneNumber, $emailAddress);

        $mockFileSystem = $this->generateMockFileSystem(2, '[{"first_name":"Firstname","last_name":"Lastname","phone":"01234 567891","email":"test@test.local"}]');
        $repository = new AddressRepository($mockFileSystem);
        $repository->persist($address);

        $result = $repository->loadById($emailAddress);
        self::assertEquals($result, $address);
    }

    public function testDelete(): void {
        $firstName = "Firstname";
        $lastName = "Lastname";
        $emailAddress = "test@test.local";
        $phoneNumber = "01234 567891";
        $address = new Address($firstName, $lastName, $phoneNumber, $emailAddress);

        $mockFileSystem = $this->generateMockFileSystem(2, '[{"first_name":"Firstname","last_name":"Lastname","phone":"01234 567891","email":"test@test.local"}]');
        $repository = new AddressRepository($mockFileSystem);
        $repository->persist($address);
        $result = $repository->loadById($emailAddress);
        self::assertNotNull($result);
        $repository->delete($result);
        self::assertNull($repository->loadById($emailAddress));

    }

}