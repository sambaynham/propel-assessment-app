<?php

namespace App\Rules;

use App\Services\Address\Infrastructure\AddressRepositoryInterface;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\ValidationException;

class UniqueEmailRule implements ValidationRule
{
    public function __construct(
        private AddressRepositoryInterface $addressRepository,
        private ? string $addressToIgnore = null
    ) {
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail('The e-mail address must be a string.');
        } elseif ($value !== $this->addressToIgnore && $this->addressRepository->loadById($value)) {
            $fail(sprintf('An entry with the e-mail address %s already exists.', $value));
        }
    }
}
