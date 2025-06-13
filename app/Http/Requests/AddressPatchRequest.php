<?php

namespace App\Http\Requests;

use App\Rules\UniqueEmailRule;
use App\Services\Address\Infrastructure\AddressRepositoryInterface;
use Illuminate\Contracts\Validation\ValidationRule;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class AddressPatchRequest extends AbstractAddressRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function rules(): array
    {
        $rules = parent::rules();

        $emailAddressToIgnore = is_string($this->input('email')) ? $this->input('email') : null;

        $rules['email'][] = new UniqueEmailRule(
            $this->container->get(
                AddressRepositoryInterface::class
            ),
            $emailAddressToIgnore
        );

        return $rules;
    }
}
