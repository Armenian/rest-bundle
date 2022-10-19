<?php
declare(strict_types=1);

namespace DMP\RestBundle\Tests\Fixtures;


class ResponseSubDTO
{
    public ?string $testSubStringField;
    public int $testSubIntField;

    public function __construct(?string $testSubStringField, int $testSubIntField)
    {
        $this->testSubStringField = $testSubStringField;
        $this->testSubIntField = $testSubIntField;
    }
}
