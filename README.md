# API Helpers for Laravel 5


This package adds the following.

- Custom request responses.

## Install
Add the repository to `composer.json`
```
"repositories": [
	{
	    "type":"vcs",
	    "url":"git@bitbucket.org:elegantmedia/laravel-api-helpers.git"
	}
]
```

```
composer require emedia/api
```

The package will be auto-discovered in Laravel 5.7.

## Usage

### Success
```
return response()->apiSuccess($transaction, $optionalMessage);
```

This will return a JSON response as,
```
{
	payload: {transactionObject or Array},
	message: '',
	result: true
}
```

### Returning a paginated response

Do this because you may need to attach a message and the result type to the message.
```
$users = User::paginate(20);
return response()->apiSuccessPaginated($users, $optionalMessage);
```

Returns
```
{
	message: 'Optional message',
	payload: [ array of objects],
	paginator: { paginationObject },
	result: true
}
```

### Unauthorized - 401, General Error - 403 (Forbidden)

```
return response()->apiUnauthorized($optionalMessage);
return response()->apiAccessDenied($optionalMessage);
```

Returns (Status code: 401 or 403)
```
{
	message: 'Optional message',
	result: false
}
```

### Generic error
```
return response()->apiError($optionalMessage, $optionalData, $optionalStatusCode);
```

Returns (Unprocessable Entity - 422 by default)
```
{
	message: 'Optional message',
	payload: {object or array},
	result: false
}
```

## Documentation Builder

You can use this package to auto-generate HTML API Specs, Swagger configurations and Postman API docs.

To allow auto-generation of docs, you need to call the `document()` function immidiately after the functions.

For example, look at the `register()` method in `AuthController` below.

```
	/**
	 *
	 * Sign up a user
	 *
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function register(Request $request)
	{
		document(function () {
			return (new APICall)->setName('Register')
				->setParams([
					(new Param('device_id', 'String', 'Unique ID of the device')),
					(new Param('device_type', 'String', 'Type of the device `APPLE` or `ANDROID`')),
					(new Param('device_push_token', 'String', 'Unique push token for the device'))->optional(),

					(new Param('first_name'))->setDefaultValue('Joe')->optional(),
					(new Param('last_name'))->setDefaultValue('Johnson')->optional(),
					(new Param('phone'))->optional(),
					(new Param('email')),

					(new Param('password', 'string',
						'Password. Must be at least 6 characters.'))->setDefaultValue('123456'),
					(new Param('password_confirmation'))->setDefaultValue('123456'),
				])
				->noDefaultHeaders()
				->setHeaders([
					(new Param('Accept', 'String', '`application/json`'))->setDefaultValue('application/json'),
					(new Param('x-api-key', 'String', 'API Key'))->setDefaultValue('123-123-123-123'),
				])
				->setSuccessObject(\App\User::class)
				->setErrorExample('{
					"message": "The email must be a valid email address.",
					"payload": {
						"errors": {
							"email": [
								"The email must be a valid email address."
							]
						}
					},
					"result": false
				}', 422);
		});

		$this->validate($request, [
			// add validation rules
		]);

		// add function logic 

		// return a single object
		$responseData = []
		return response()->apiSuccess($responseData);
	}
```

The above example defines and returns a single object. If you want to return a paginated list of objects, use the pagination methods instead.

Example
```
	// definition
	...
		->setGroup('Properties')
		->setSuccessPaginatedObject(Property::class)
		->setSuccessExample('')
	...
	
	// response
	$paginator = Property::paginate();
	return response()->apiSuccessPaginated($paginator);
```

### Define Additional Response Fields

Swagger depends on the response fields that you send back. This generator script will read the Models and build the fields automatically. If you'd like to define any custom fields, add them to the Model with the `getExtraApiFields()` method. Note how you can add arrays and objects. This will be helpful when adding related entities as nested objects.

Example: Add 4 new fields to the response object as `is_active`, `access_token`, `author` and `comments` with different data types.
```
class User extends Authenticatable
{

	public function getExtraApiFields()
	{
		return [
			'is_active' => 'boolean',
			'access_token',	// if the data type is not given, it will default to `string`
			'author' => ['type' => 'object', 'items' => 'User'],	// User object should be exposed to API generator in a separate endpoint eg: /users
			'comments' => ['type' => 'array', 'items' => 'Comment'], // User object should be exposed to API generator in a separate endpoint eg: /post/:id/comments
		];
	}
	
}
```


After all API definitions are included, call the Generator with,
```
php artisan generate:docs
```

The screen will output the generated filenames.  If you have installed [ApiDocs.js](http://apidocjs.com/), then to complete the HTML documentation, run
```
apidoc -i resources/docs -o public_html/docs/api
```

## Notes About Documentation

- The `APICall` and `APIParam` classes will use many defaults, and guess the HTTP method types, group names etc. But they can be customised. See the auto-completion methods with an IDE such as PHPStorm.
- The `APICall` will auto-insert the authenticated user headers with `x-access-token`. If you don't want this to be included, call `noDefaultHeaders()` method and then specify your own headers with `setHeaders()`.
- The `swagger.json` file will also contain model definitions if the models are included in the `app\Entities` folder. You can disable this feature from `config/oxygen.php` file. You can also hide unwanted model definitions from there.

## Postman Collections and Environments

- The generator will output a Postman Collection file and a separate Environment file. These files can be directly imported to Postman to fast and easy testing of the API.
- See the videos below for more information on this feature.
- [Importing API as a Postman Collection](https://youtu.be/WQwYNu4PCpg?t=73)
- [Postman Environments](https://youtu.be/M3QAjLTqC9c)