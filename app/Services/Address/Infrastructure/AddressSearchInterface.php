<?php

namespace App\Services\Address\Infrastructure;

interface AddressSearchInterface
{
    /**
     * @param string $searchTerms
     * @return iterable<SampleDto>
     */
    public function search(string ...$searchTerms): iterable;
}