<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AddressPatchRequest;
use App\Http\Requests\AddressPostRequest;
use App\Services\Address\Domain\Address;
use App\Services\Address\Infrastructure\AddressRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;


class AddressController extends Controller
{
    public function __construct(private AddressRepositoryInterface $addressRepository) {

    }
    public function index(Request $request): mixed {
        $pageVars = [
            'pageTitle' => 'Addresses',
            'addresses' => $this->addressRepository->findAll()
        ];
        return view('address.index', $pageVars);
    }

    public function create(Request $request): mixed {
        $pageVars = [
            'pageTitle' => 'Create a new Address',
            'addresses' => $this->addressRepository->findAll()
        ];
        return view('address.create', $pageVars);
    }

    public function post(AddressPostRequest $request): RedirectResponse
    {
        $address = new Address(
            $request->input('first_name'),
            $request->input('last_name'),
            $request->input('phone'),
            $request->input('email')
        );
        $this->addressRepository->persist($address);
        return redirect()->route('address.index')->with('success', 'Address has been created');
    }

    public function edit(string $id): mixed {
        $address = $this->addressRepository->loadById(urldecode($id));
        if (null === $address) {
            abort(404);
        }
        $pageVars = [
            'pageTitle' => sprintf("Editing '%s'", $address->getEmail()),
            'address' => $address
        ];
        return view('address.edit', $pageVars);
    }

    public function patch(AddressPatchRequest $request, string $id): RedirectResponse {
        $address = $this->addressRepository->loadById(urldecode($id));
        if (null === $address) {
            abort(404);
        }
        $address->setFirstName($request->input('first_name'));
        $address->setLastName($request->input('last_name'));
        $address->setPhone($request->input('phone'));
        $address->setEmail($request->input('email'));
        $this->addressRepository->persist($address);
        return redirect()->route('address.index')->with('success', sprintf("Address %s has been updated", $address->getEmail()));
    }

    public function deleteConfirm(Request $request, string $id): mixed {
        $address = $this->addressRepository->loadById(urldecode($id));
        if (null === $address) {
            abort(404);
        }
        $pageVars = [
            'pageTitle' => sprintf("Are you sure you want to delete '%s'?", $address->getEmail()),
            'address' => $address
        ];
        return view('address.delete', $pageVars);
    }

    public function delete(Request $request, string $id): RedirectResponse {
        $address = $this->addressRepository->loadById(urldecode($id));
        if (null === $address) {
            abort(404);
        }
        $this->addressRepository->delete($address);
        return redirect()->route('address.index')->with('success', sprintf("Address %s has been deleted", $address->getEmail()));
    }

}
