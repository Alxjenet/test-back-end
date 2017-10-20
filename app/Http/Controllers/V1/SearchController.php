<?php

namespace App\Http\Controllers\V1;

use App\Services\SearchService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class SearchController
 * @package App\Http\Controllers\V1
 */
class SearchController extends ApiController
{
    /**
     * @var SearchService
     */
    private $searchService;

    /**
     * SearchController constructor.
     * @param SearchService $searchService
     */
    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $queryParams = $request->query->all();
            return $this->searchService->getAircrafts($queryParams['from'], $queryParams['to']);
        } catch (Exception $exception) {
            return $this->getResponse(['error' => 'Bad request', 'message' => $exception->getMessage()])->setStatusCode(JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
