# REST Bundle

This bundle provides convenient functionality to write simple and powerful JSON-based RPC controllers

## How to use:

Add bundle `DMP\RestBundle\RestBundle` to your kernel.

Write controllers in a style:

```php
use DMP\RestBundle\Annotation as Rest;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class TestController
{

    #[Route("/api/test", methods: ["POST"])]
    #[Rest\Serializable(statusCode: 201)]
    public function request(#[MapRequestPayload] RequestDTO $request): ResponseDTO
    {
        ...
    }
}
```
where RequestDTO and ResponseDTO are Data Transfer Objects that should define Serialization and Validation rules for the fields

All routes should start with '/api/' (TODO: remove this hard-coded requirement)

Consult `tests/Fixtures/` directory for an overview of how to define Rest Controllers

## Request Data Mapping
Use Symfony's native attributes for mapping request data to DTOs:
- `#[MapRequestPayload]` for JSON request body.
- `#[MapQueryString]` for query parameters.

The bundle's `ExceptionListener` will automatically catch validation errors from these attributes and format them into a consistent JSON response.

## Exceptions
If the controller throws an exception, it will be converted into a response in the form
```json
{
  "errors": [
    {
      "message": "Exception message"
    }
  ]
}
```

Validation error responses are different:
```json
{
  "errors": [
    {
      "message": "Field value should be a valid email address.",
      "field": "email",
      "parameters": {}
    },
    {
      "message": "Field value should not be blank.",
      "field": "password",
      "parameters": {}
    }
  ]
}
```
