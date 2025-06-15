<?php

namespace App\Rules;

use App\Services\Address\Infrastructure\AddressRepositoryInterface;
use App\Services\Address\Service\AddressServiceInterface;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\ValidationException;

class UniqueEmailRule implements ValidationRule
{
    public function __construct(
        private AddressServiceInterface $addressService,
        private ? string $idToIgnore = null
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
        } else {
            $existingAddress = $this->addressService->loadByEmail($value);
            if ($existingAddress && $existingAddress->getId() !== $this->idToIgnore) {
                $fail(sprintf('An entry with the e-mail address %s already exists.', $value));
            }
        }
    }
}
