<?php

declare(strict_types=1);

namespace DMP\RestBundle\Validation;

enum ValidationErrorType: string
{
    case BODY = 'body';
    case QUERY = 'query';
}
