<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchRequest;
use App\Services\Search\ElasticSearchService;
use Symfony\Component\HttpFoundation\Response;

class ApiSearchController extends Controller
{
    public function __construct(
        private ElasticSearchService $searchService
    ) {}
    private const string SEARCH_SANITIZE_PATTERN = '/[^A-Za-z0-9 @ +_\-.]/';

    public function post(SearchRequest $request): mixed {
        $searchTerms = $request->input('search-terms');
        if (!is_string($searchTerms)) {
            return \response()->json(['error' => 'search-terms must be a string'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $searchTerms = preg_replace(self::SEARCH_SANITIZE_PATTERN, '', $searchTerms);
        if (is_string($searchTerms)) {
            $searchTerms = trim($searchTerms);
            $searchTerms = explode(' ', $searchTerms);
            $results = $this->searchService->search(...$searchTerms);
        }

        return \response()->json($results ?? []);
    }
}
