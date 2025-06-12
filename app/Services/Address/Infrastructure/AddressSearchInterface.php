<?php

namespace App\Services\Address\Infrastructure;

use App\Services\Address\Domain\Address;

interface AddressSearchInterface
{
    /**
     * @param string ...$searchTerms
     * @return iterable<Address>
     */
    public function search(string ...$searchTerms): iterable;
}