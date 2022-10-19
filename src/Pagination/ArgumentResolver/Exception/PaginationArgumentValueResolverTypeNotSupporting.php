<?php

declare(strict_types=1);


namespace DMP\RestBundle\Pagination\ArgumentResolver\Exception;

use Exception;

class PaginationArgumentValueResolverTypeNotSupporting extends Exception
{
    public function __construct(private readonly string $type)
    {
        parent::__construct(
            sprintf('Pagination argument value resolver type [%s] not supporting', $this->type)
        );
    }
}
