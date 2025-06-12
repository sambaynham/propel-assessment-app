<?php

declare(strict_types=1);

namespace App\Services\Address\Infrastructure;

use App\Services\Address\Domain\Address;
use App\Services\Address\Infrastructure\Exceptions\ReadException;
use App\Services\Address\Infrastructure\Exceptions\WriteException;
use App\Services\Address\Infrastructure\Exceptions\MappingException;
use Illuminate\Contracts\Filesystem\Filesystem;


class AddressRepository implements AddressRepositoryInterface, AddressSearchInterface
{
    private const string FILE_NAME = 'persistence.json';

    private const array REQUIRED_FIELDS = [
        'first_name',
        'last_name',
        'phone',
        'email'
    ];

    /**
     * @throws MappingException|WriteException|ReadException
     */
    public function __construct(private readonly Filesystem $filesystem) {
        if (!$this->filesystem->exists(self::FILE_NAME)) {
            $this->makeFile();
        }
        $this->collection = $this->decodeFile();
    }


    /**
     * By memoizing our data set like this, we keep our 'database' in memory, allowing for much quicker searching.
     *
     * It ain't pretty, but it works (Until we add too many data and overwhelm system memory)
     *
     * @var array<Address>
     */
    private array $collection;


    /**
     * @param Address $address
     * @return void
     * @throws WriteException
     */
    public function persist(Address $address): void {
        $itemExists = false;
        foreach ($this->collection as $delta => $item) {
            if ($item->getEmail()  === $address->getEmail()) {
                $this->collection[$delta] = $address;
                $itemExists = true;
            }
        }
        if (!$itemExists) {
            $this->collection[] = $address;
        }

        $this->writeFile();
    }

    /**
     * @param Address $address
     * @return void
     */
    public function delete(Address $address): void {
        foreach ($this->collection as $delta => $item) {
            if ($item->getEmail() === $address->getEmail()) {
                unset($this->collection[$delta]);
            }
        }
    }

    /**
     * @param string $identifier
     *
     * @return Address|null
     */
    public function loadById(string $identifier): ? Address {

        $item = \array_find($this->collection, fn($item) => $item->getEmail() === $identifier);
        return $item instanceof Address ? $item : null;
    }

    /**
     * If we don't have a working file, create it and encode an empty array in it.
     *
     * @return void
     * @throws WriteException
     */
    private function makeFile(): void {
        $emptyResult = json_encode([]);
        if ($emptyResult === false) {
            throw new WriteException("Json Encode failed. Are the correct extensions installed?");
        }
        $this->filesystem->put(self::FILE_NAME, $emptyResult);

    }

    /**
     * @return array<Address>
     *
     * @throws MappingException|ReadException
     */
    private function decodeFile(): array {

        $fileContents = $this->filesystem->get(self::FILE_NAME);
        if ($fileContents === null) {
            throw new ReadException("File could not be read.");
        }
        $arrayResults = json_decode($fileContents, true);
        if ($arrayResults === false || !is_array($arrayResults)) {
            throw new ReadException("Could not read from persistence JSON. Please check storage layer.");
        }

        $mappedResults = [];

        foreach ($arrayResults as $arrayResult) {
            //@var array{'first_name': string, "last_name": string, "phone": string, "email": string} arrayResult
            $this->validateResult($arrayResult);
            $mappedResults[] = $this->map($arrayResult);
        }
        return $mappedResults;
    }

    /**
     * Commit the current contents of the collection to storage.
     *
     * @return void
     * @throws WriteException
     */
    private function writeFile(): void {
        $encodedCollection = json_encode($this->collection);
        if ($encodedCollection === false) {
            throw new WriteException("Json Encode failed. Are the correct extensions installed?");
        }
        $this->filesystem->put(self::FILE_NAME, $encodedCollection);
    }

    /**
     * @param array{'first_name': string, "last_name": string, "phone": string, "email": string} $result
     * @return Address
     */
    private function map(array $result): Address {
       return new Address($result["first_name"], $result["last_name"], $result["phone"], $result["email"]);
    }


    /**
     * @param mixed $result
     * @return void
     * @throws MappingException
     */
    private function validateResult(mixed $result): void {
        if (!is_array($result)) {
            throw new MappingException("Could not validate result: result is not an array.");
        }
        foreach (self::REQUIRED_FIELDS as $fieldName) {
            if (!isset($result[$fieldName])) {
                throw new MappingException(sprintf("The field %s is required", $fieldName));
            }
        }
    }

    /**
     * This is a terrible, clunky, naive search.
     *
     * The only thing that makes it even remotely workable is that our 'database' is memoized.
     * It will work, while our result set remains small, but it should  be replaced with something like SOLR
     *
     * Fortunately, it's defined in a separate interface, so that's a very easy thing to do.
     *
     * @param string ...$searchTerms
     *
     * @return array<Address>
     */
    public function search(string ...$searchTerms): iterable {
        $results = [];
        foreach ($this->collection as $item) {
            foreach ($searchTerms as $searchTerm) {
                if (str_contains($item->getFirstName(), $searchTerm) || str_contains($item->getLastName(), $searchTerm)) {
                    $results[] = $item;
                }
            }
        }
        return $results;
    }
}
