<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
abstract class AbstractAddressRequest extends FormRequest
{
    protected const string UK_PHONE_REGEX = '/^(\S+)?((((\+44\s?([0–6]|[8–9])\d{3} | \(?0([0–6]|[8–9])\d{3}\)?)\s?\d{3}\s?(\d{2}|\d{3}))|((\+44\s?([0–6]|[8–9])\d{3}|\(?0([0–6]|[8–9])\d{3}\)?)\s?\d{3}\s?(\d{4}|\d{3}))|((\+44\s?([0–6]|[8–9])\d{1}|\(?0([0–6]|[8–9])\d{1}\)?)\s?\d{4}\s?(\d{4}|\d{3}))|((\+44\s?\d{4}|\(?0\d{4}\)?)\s?\d{3}\s?\d{3})|((\+44\s?\d{3}|\(?0\d{3}\)?)\s?\d{3}\s?\d{4})|((\+44\s?\d{2}|\(?0\d{2}\)?)\s?\d{4}\s?\d{4})))$/';

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
                sprintf('regex:%s',self::UK_PHONE_REGEX),
                'required',
            ],
            'email' => [
                'email',
                'required',
            ]
        ];
    }
}