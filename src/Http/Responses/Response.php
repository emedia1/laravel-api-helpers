<?php
namespace EMedia\Api\Http\Responses;

use Illuminate\Http\Response as BaseResponse;

class Response extends BaseResponse
{

	// custom status codes
	const HTTP_AUTH_EXPIRED = 440;		// Non-RFC code, from Microsoft (Session has expired)
	const HTTP_UNPROCESSABLE_ENTITY = 422;

}