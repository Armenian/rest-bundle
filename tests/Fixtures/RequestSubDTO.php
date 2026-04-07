<?php

declare(strict_types=1);

namespace DMP\RestBundle\Tests\Fixtures;

use Symfony\Component\Validator\Constraints as Assert;

class RequestSubDTO
{
    #[Assert\NotBlank(message: 'BLANK_SUB_VALIDATION_MESSAGE')]
    public ?string $testSubStringField = null;

    public int $testSubIntField;
}
