<?php

declare(strict_types=1);

namespace App\Http\Controllers\Visitor;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddressPatchRequest;
use App\Http\Requests\AddressPostRequest;
use App\Services\Address\Domain\Address;
use App\Services\Address\Service\AddressServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;


class AddressController extends Controller
{
    public function __construct(private readonly AddressServiceInterface $addressService) {

    }
    public function index(Request $request): mixed {
        $pageVars = [
            'pageTitle' => 'Addresses',
            'addresses' => $this->addressService->findAll(),
            'breadcrumbs' => [
                [
                    'path' => route('visitor.address.index'),
                    'label' =>  'Addresses',
                    'active' => false
                ]
            ]
        ];
        return view('address.index', $pageVars);
    }

    public function get(Request $request, string $id): mixed {
        $address = $this->addressService->loadById(urldecode($id));
        if (null === $address) {
            abort(404);
        }
        $pageVars = [
            'pageTitle' => sprintf("Viewing '%s'", $address->getEmail()),
            'address' => $address,
            'breadcrumbs' => [
                [
                    'path' => route('visitor.address.index'),
                    'label' =>  'Addresses',
                    'active' => false
                ],
                [
                    'path' => route('visitor.address.get', ['id' => $address->getId()]),
                    'label' =>  $address->getEmail(),
                    'active' => true
                ]
            ]
        ];
        return view('address.view', $pageVars);
    }

    public function create(Request $request): mixed {
        $pageVars = [
            'pageTitle' => 'Create a new Address',
            'addresses' => $this->addressService->findAll(),
            'breadcrumbs' => [
                [
                    'path' => route('visitor.address.index'),
                    'label' =>  'Addresses',
                    'active' => false
                ],
                [
                    'path' => '',
                    'label' => 'Create',
                    'active' => true
                ]
            ]
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
        $this->addressService->save($address);
        return redirect()->route('visitor.address.index')->with('success', 'Address has been created');
    }

    public function edit(string $id): mixed {
        $address = $this->addressService->loadById(urldecode($id));
        if (null === $address) {
            abort(404);
        }
        $pageVars = [
            'pageTitle' => sprintf("Editing '%s'", $address->getEmail()),
            'address' => $address,
            'breadcrumbs' => [
                [
                    'path' => route('visitor.address.index'),
                    'label' =>  'Addresses',
                    'active' => false
                ],
                [
                    'path' => route('visitor.address.get', ['id' => $address->getId()]),
                    'label' =>  $address->getEmail(),
                    'active' => true
                ],
                [
                    'path' => route('visitor.address.edit', ['id' => $address->getId()]),
                    'label' =>  'Edit',
                    'active' => true
                ]
            ]
        ];
        return view('address.edit', $pageVars);
    }

    public function patch(AddressPatchRequest $request, string $id): RedirectResponse {
        $address = $this->addressService->loadById(urldecode($id));
        if (null === $address) {
            abort(404);
        }
        $address->setFirstName($request->input('first_name'));
        $address->setLastName($request->input('last_name'));
        $address->setPhone($request->input('phone'));
        $address->setEmail($request->input('email'));
        $this->addressService->save($address);
        return redirect()->route('visitor.address.index')->with('success', sprintf("Address %s has been updated", $address->getEmail()));
    }

    public function deleteConfirm(Request $request, string $id): mixed {
        $address = $this->addressService->loadById(urldecode($id));
        if (null === $address) {
            abort(404);
        }
        $pageVars = [
            'pageTitle' => sprintf("Are you sure you want to delete '%s'?", $address->getEmail()),
            'address' => $address,
            'breadcrumbs' => [
                [
                    'path' => route('visitor.address.index'),
                    'label' =>  'Addresses',
                    'active' => false
                ],
                [
                    'path' => route('visitor.address.get', ['id' => $address->getId()]),
                    'label' =>  $address->getEmail(),
                    'active' => true
                ],
                [
                    'path' => route('visitor.address.deleteConfirm', ['id' => $address->getId()]),
                    'label' =>  'Delete',
                    'active' => true
                ]
            ]
        ];
        return view('address.delete', $pageVars);
    }

    public function delete(Request $request, string $id): RedirectResponse {
        $address = $this->addressService->loadById(urldecode($id));
        if (null === $address) {
            abort(404);
        }
        $this->addressService->delete($address);
        return redirect()->route('visitor.address.index')->with('success', sprintf("Address %s has been deleted", $address->getEmail()));
    }

}
