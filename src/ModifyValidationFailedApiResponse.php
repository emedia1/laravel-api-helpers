<?php
namespace EMedia\Api;

trait ModifyValidationFailedApiResponse
{

	/**
	 * Validate the given request with the given rules.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  array  $rules
	 * @param  array  $messages
	 * @param  array  $customAttributes
	 * @return array
	 *
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function validate(\Illuminate\Http\Request $request, array $rules,
		array $messages = [], array $customAttributes = [])
	{
		try {
			return parent::validate($request, $rules, $messages, $customAttributes);
		} catch (\Illuminate\Validation\ValidationException $ex) {
			if ($request->expectsJson() && !$request->pjax()) {
				$errorMessages = array_flatten($ex->errors());
				$mergedErrorMessages = implode(' ', $errorMessages);
				$data = [
					'errors' => $ex->errors(),
				];
				throw new \Illuminate\Http\Exceptions\HttpResponseException(response()->apiError($mergedErrorMessages, $data, \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY));
			}

			throw $ex;
		}
	}

}