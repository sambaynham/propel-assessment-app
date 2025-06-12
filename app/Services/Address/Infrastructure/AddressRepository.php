<?php

declare(strict_types=1);

namespace App\Services\Address\Infrastructure;

use App\Services\Address\Domain\Address;
use App\Services\Address\Infrastructure\Exceptions\MappingException;
use Illuminate\Contracts\Filesystem\Filesystem;


class AddressRepository implements AddressRepositoryInterface, AddressSearchInterface
{

    public function __construct(private Filesystem $filesystem) {
        if (!$this->filesystem->exists(self::FILE_NAME)) {
            $this->makeFile();
        }
        $this->collection = $this->decodeFile();
    }
    private const string FILE_NAME = 'persistence.json';

    private const array REQUIRED_FIELDS = [
        'first_name',
        'last_name',
        'phone',
        'email'
    ];

    /**
     * By memoizing our data set like this, we keep our 'database' in memory, allowing for much quicker searching.
     *
     * It ain't pretty, but it works (Until we add too many data and overwhelm system memory)
     *
     * @var array<Address>
     */
    private array $collection;


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
     * @param string $identifier
     *
     * @return Address|null
     */
    public function loadById(string $identifier): ? Address {

        return array_find($this->collection, fn($item) => $item->getEmail() === $identifier);

    }

    /**
     * If we don't have a working file, create it and encode an empty array in it.
     * @return void
     */
    private function makeFile(): void {
        $this->filesystem->put(self::FILE_NAME, json_encode([]));

    }

    /**
     * @return array<Address>
     *
     * @throws MappingException
     */
    private function decodeFile(): array {

        $arrayResults = json_decode($this->filesystem->get(self::FILE_NAME), true);
        $mappedResults = [];
        foreach ($arrayResults as $delta => $arrayResult) {
            $mappedResults[$delta] = $this->map($arrayResult);
        }
        return $mappedResults;
    }

    /**
     * Commit the current contents of the collection to storage.
     *
     * @return void
     */
    private function writeFile(): void {
        $this->filesystem->put(self::FILE_NAME, json_encode($this->collection));
    }

    /**
     * @throws MappingException
     */
    private function map(array $result): Address {

       $this->validateArrayResult($result);
       return new Address($result["first_name"], $result["last_name"], $result["phone"], $result["email"]);
    }

    /**
     * @throws MappingException
     */
    private function validateArrayResult(array $arrayResult): void {
        foreach (self::REQUIRED_FIELDS as $fieldName) {
            if (!isset($arrayResult[$fieldName])) {
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
        foreach ($this->collection as $delta => $item) {
            foreach ($searchTerms as $searchTerm) {
                if (str_contains($item->getFirstName(), $searchTerm) || str_contains($item->getLastName(), $searchTerm)) {
                    $results[] = $item;
                }
            }
        }
        return $results;
    }
}
