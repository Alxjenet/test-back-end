<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * Class ApiController
 * @package App\Http\Controllers
 */
abstract class ApiController extends Controller
{
    protected function getResponse($data, $status = JsonResponse::HTTP_OK, $headers = [], $options = 0)
    {
        return new JsonResponse($data, $status, $headers, $options);
    }
}