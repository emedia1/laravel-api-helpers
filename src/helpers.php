<?php

if (!function_exists('document')) {
	/**
	 *
	 * Add a call to API documentation
	 *
	 * @param Closure $closure
	 * @return boolean
	 */
	function document(Closure $closure)
	{
		if (!env('DOCUMENTATION_MODE', false)) return;

		$apiRequest = $closure();

		/** @var \EMedia\Api\Docs\DocBuilder $docBuilder */
		$docBuilder = app('emedia.api.builder');

		/** @var \EMedia\Api\Docs\APICall $apiRequest */
		$docBuilder->register($apiRequest);


		if (env('DOCUMENTATION_MODE')) {
			$docBuilder->throwDocumentationModeException();
		}

		return true;
	}
}