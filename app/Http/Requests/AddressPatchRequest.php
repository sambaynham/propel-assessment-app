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
        $rules['id'] = [
            'string',
            'required',
        ];
        $idToIgnore = is_string($this->input('id')) ? $this->input('id') : null;
        $rules['email'][] = new UniqueEmailRule(
            $this->container->get(
                AddressRepositoryInterface::class
            ),
            $idToIgnore
        );

        return $rules;
    }
}
