<?php


namespace EMedia\Api\Docs;


use EMedia\Api\Exceptions\DocumentationModeEnabledException;
use Illuminate\Support\Collection;

class DocBuilder
{

	protected $apiCalls;
	protected $attributes = [];

	public function __construct()
	{
		$this->apiCalls = new Collection();
	}

	/**
	 *
	 * Register an API Call with the Doc Builder
	 *
	 * @param APICall $apiCall
	 *
	 * @throws \ReflectionException
	 */
	public function register(APICall $apiCall)
	{
		if (!env('DOCUMENTATION_MODE', false)) {
			return;
		}

		// set defines or uses
		$define = $apiCall->getDefine();
		if (!empty($define)) {
			$group = $apiCall->getGroup();
			if (empty($group)) {
				$apiCall->setGroup(snake_case($define['title']));
			}
			$this->apiCalls->push($apiCall);
			return;
		} else {
			if ($apiCall->isAddDefaultHeaders()) {
				$apiCall->setUse('default_headers');
			}
		}

		if (empty($apiCall->getRoute())) {
			if (!empty($this->attributes['uri'])) {
				$apiCall->setRoute($this->attributes['uri']);
			} else {
				throw new \Exception("The route must be set for the API call");
			}
		} else {
			// $apiCall->setRoute('api/v1/' . $apiCall->getRoute());
		}

		if (empty($apiCall->getMethod())) {
			if (!empty($this->attributes['method'])) {
				$apiCall->setMethod($this->attributes['method']);
			} else {
				$apiCall->setMethod('get');
			}
		}

		// set default group
		$group = $apiCall->getGroup();
		if (empty($group)) {
			// Get the full controller name and extract the Prefix from {Prefix}Controller as the default group
			if (isset($this->attributes['action'])) {
				$parts = explode('@', $this->attributes['action']);
				$reflection = new \ReflectionClass($parts[0]);
				if ($reflection) {
					$group = str_replace('Controller', '', $reflection->getShortName());
					$apiCall->setGroup($group);
				}
			}
		}

		// try to set a default name
		$name = $apiCall->getName();
		if (empty($name)) {
			$singularGroup = str_singular($group);
			$method = strtolower($apiCall->getMethod());
			switch ($method) {
				case 'post':
					$newName = "Create a $singularGroup";
					break;
				case 'delete':
					$newName = "Delete a $singularGroup";
					break;
				case 'put':
				case 'patch':
					$newName = "Update a $singularGroup";
					break;
				case 'get':
				default:
					$newName = "Get a $singularGroup";
					if (isset($this->attributes['action'])) {
						$action = strtolower($this->attributes['action']);
						if (strpos($action, 'search') !== false) {
							$newName = "List {$group}";
						}
						if (strpos($action, 'index') !== false) {
							$newName = "Search $singularGroup";
						}
					}
					break;
			}

			if (empty($newName)) $newName = '<UNKNOWN NAME>';
			$apiCall->setName($newName);
		}

		// if there's still no group, set a default group
		if (empty($group)) {
			$apiCall->setGroup('Misc');
		}

		$this->apiCalls->push($apiCall);
	}

	public function findByDefinition($defineName)
	{
		$apiCalls = $this->apiCalls->filter(function (APICall $item) use ($defineName) {
			$define = $item->getDefine();
			if (isset($define['title'])) {
				return $define['title'] === $defineName;
			}
			return false;
		});

		if ($apiCalls->isNotEmpty()) {
			return $apiCalls->first();
		}

		return null;
	}

	public function setInterceptor($method, $uri, $action)
	{
		$this->attributes['method'] = $method;
		$this->attributes['uri'] = $uri;
		$this->attributes['action'] = $action;
	}

	public function clearInterceptor()
	{
		$this->attributes = [];
	}

	public function getApiCalls()
	{
		return $this->apiCalls;
	}

	public function throwDocumentationModeException()
	{
		throw new DocumentationModeEnabledException("Requests cannot be executed while in documentation mode.");
	}

}