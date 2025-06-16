<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Services\Address\Domain\Address;
use Illuminate\Foundation\Http\FormRequest;
abstract class AbstractAddressRequest extends FormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array{'first_name': array<string>, 'last_name':array<string>, 'phone': array<string>, 'email': array<string>}
     */
    public function rules(): array
    {
        return [
            'first_name' => ['string','required'],
            'last_name' => ['string','required'],
            'phone' => [
                sprintf('regex:%s',Address::UK_PHONE_REGEX),
                'required',
            ],
            'email' => [
                'email',
                'required',
            ]
        ];
    }
}