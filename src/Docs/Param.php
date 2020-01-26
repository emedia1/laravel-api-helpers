<?php


namespace EMedia\Api\Docs;


class Param
{

	const LOCATION_HEADER = 'header';
	const LOCATION_FORM = 'formData';
	const LOCATION_COOKIE = 'cookie';
	const LOCATION_PATH = 'path';
	const LOCATION_QUERY = 'query';
	const LOCATION_BODY = 'body';

	protected $fieldName;
	protected $required = true;
	protected $dataType;
	protected $defaultValue;
	protected $description = '';
	protected $location;
	protected $model;

	public function __construct($fieldName = null, $dataType = 'String', $description = null, $location = null)
	{
		$this->fieldName = $fieldName;
		$this->dataType = $dataType;
		$this->location = $location;
		if (!$description && $fieldName) {
			$this->description = ucfirst(reverse_snake_case($fieldName));
		} else {
			$this->description = $description;
		}
	}

	/**
	 * @return bool
	 */
	public function getRequired(): bool
	{
		return $this->required;
	}

	/**
	 * @param bool $required
	 */
	public function required()
	{
		$this->required = true;

		return $this;
	}

	public function optional()
	{
		$this->required = false;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getDataType(): string
	{
		return ucfirst($this->dataType);
	}

	/**
	 * @param string $dataType
	 */
	public function dataType(string $dataType)
	{
		$this->dataType = $dataType;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getDefaultValue()
	{
		return $this->defaultValue;
	}

	/**
	 * @param mixed $defaultValue
	 */
	public function defaultValue($defaultValue)
	{
		$this->defaultValue = $defaultValue;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return $this->description;
	}

	/**
	 * @param string $description
	 */
	public function description(string $description)
	{
		$this->description = $description;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getName()
	{
		return $this->fieldName;
	}

	/**
	 * @param mixed $fieldName
	 */
	public function field($fieldName)
	{
		$this->fieldName = $fieldName;

		return $this;
	}

	/**
	 * @return null|string
	 */
	public function getLocation()
	{
		return $this->location;
	}

	/**
	 * @param string $location
	 */
	public function setLocation(string $location)
	{
		$this->location = $location;

		return $this;
	}

	/**
	 * @param object $model
	 */
	public function setModel($model)
	{
		$this->model = $model;

		return $this;
	}

	/**
	 * @return object
	 */
	public function getModel()
	{
		return $this->model;
	}

	/**
	 * @param mixed $defaultValue
	 *
	 * @return Param
	 */
	public function setDefaultValue($defaultValue)
	{
		$this->defaultValue = $defaultValue;

		return $this;
	}

	/**
	 * @param $dataType
	 *
	 * @return string
	 */
	public static function getSwaggerDataType($dataType)
	{
		$dataType = strtolower($dataType);

		switch ($dataType) {
			case 'integer':
				return 'integer';
				break;
			case 'float':
			case 'double':
				return 'number';
				break;
			case 'boolean':
				return 'boolean';
				break;
			case 'array':
				return 'array';
			case 'object':
			case 'model':
				return 'object';
				break;
			case 'string':
			case 'datetime':
			case 'file':
			case 'date':
			case 'text':
			default:
				return 'string';
		}
	}

}
