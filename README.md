# REST Bundle

This bundle provides convenient functionality to write simple and powerful JSON-based RPC controllers

## How to use:

Add bundle `DMP\RestBundle\RestBundle` to your kernel.

Write controllers in a style:

```
class TestController
{

    #[Rest\Post("")]
    #[BodyConverter("request")]
    #[Rest\Serializable(statusCode: 201)]
    public function request(string $testCode, RequestDTO $request): ResponseDTO
    {
        ...
    }
}
```
where RequestDTO and ResponseDTO are Data Transfer Objects that should define Serialization and Validation rules for the fields

All routes should start with '/api/' (TODO: remove this hard-coded requirement)

Consult `tests/Fixtures/` directory for an overview of how to define Rest Controllers

## BodyConverter
Put `@BodyConverter` annotation (`DMP\RestBundle\Annotations\BodyConverter`) on a controller's action.
Put the name of a DTO argument as a default argument to the annotation.

This will have the following effect:
The `@dmp_rest.converter.request_body` service will try to deserialize the request body into the class of the DTO argument.
The deserialized DTO is then validated (Symfony Validator is used, configure accordingly)
The validated deserialized DTO is going to be passed as a value of the DTO argument in controller action.

## Exceptions
Any validation error by default throws an exception (`DMP\RestBundle\Validation\ValidationException` to be precise). 
This is achieved via `@dmp_rest.validation_exception_throwing_body_converter` service (decorator).

An important caveat is that it relies on value of `dmp_rest.body_converter.validation_errors_argument` to be `validationErrors` 
(which is defined as a `const` `DMP\RestBundle\Validation\ValidationExceptionThrowingBodyConverterDecorator::VALIDATION_ERRORS_ARGUMENT_NAME`)
Overriding that value will disable the decorator's ability to throw an exception which would cause unvalidated DTOs to be passed into controller actions.

If controller throws exception it will be converted into a response in the form
```json
{
  "errors": [
    {
      "message": "Exception message",
    }
  ]
}
```

`DMP\RestBundle\Validation\ValidationException` is treated differently:
```json
{
  "errors": [
    {
      "message": "Field value should be a valid email address.",
      "type": "body",
      "field": "email"
    },
    {
      "message": "Field value should not be blank.",
      "type": "body",
      "field": "password"
    }
  ]
}
```
