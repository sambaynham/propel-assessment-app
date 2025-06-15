<?php

declare(strict_types=1);

namespace App\Http\Controllers\Visitor;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchRequest;
use App\Services\Address\Infrastructure\AddressSearchInterface;
use Symfony\Component\HttpFoundation\Response;

class SearchController extends Controller
{
    public function __construct(private AddressSearchInterface $searchService) {}
    private const string SEARCH_SANITIZE_PATTERN = '/[^A-Za-z0-9 @ +_\-.]/';

    public function post(SearchRequest $request): mixed {
        $searchTerms = $request->input('search-terms');
        if (!is_string($searchTerms)) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $searchTerms = preg_replace(self::SEARCH_SANITIZE_PATTERN, '', $searchTerms);
        if (is_string($searchTerms)) {
            $searchTerms = trim($searchTerms);
            $searchTerms = explode(' ', $searchTerms);
            $results = $this->searchService->search(...$searchTerms);
        }


        $pageVars = [
            'pageTitle' => 'Search Results',
            'results' => $results ?? [],
            'breadcrumbs' => [
                [
                    'path' => route('visitor.address.index'),
                    'label' =>  'Addresses',
                    'active' => false
                ],
                [
                    'path' => route('visitor.address.index'),
                    'label' =>  'Search Results',
                    'active' => false
                ],
            ]
        ];
        return view('search.results', $pageVars);
    }
}
