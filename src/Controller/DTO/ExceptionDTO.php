<?php

declare(strict_types=1);

namespace DMP\RestBundle\Controller\DTO;


class ExceptionDTO
{
    /**
     * @var array|Error[]
     */
    public array $errors = [];
}
