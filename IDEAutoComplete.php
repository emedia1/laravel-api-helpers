<?php

namespace Illuminate\Contracts\Routing;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\JsonResponse;

/**
 * @method JsonResponse apiSuccess($payload = null, $message = '') Send an API success response.
 * @method JsonResponse apiSuccessPaginated(Paginator $paginator, $message = '', $customData = []) Send a paginated response.
 *
 * @method JsonResponse apiErrorUnauthorized($message = null) Send 401 response.
 * @method JsonResponse apiErrorAccessDenied($message = null) Send 403 response.
 * @method JsonResponse apiError($message = null, $payload = null, $statusCode = 422) Send a generic error response.
 */
interface ResponseFactory
{
}
