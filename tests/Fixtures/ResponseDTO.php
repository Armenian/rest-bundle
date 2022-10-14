<?php
declare(strict_types=1);

namespace DMP\RestBundle\Tests\Fixtures;


use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;

class ResponseDTO
{
    public ?string $testStringField;
    public ResponseSubDTO $testSubField;
    public array $testArrayField;
    public ?string $testEmailField;
    public ?string $testCode;
    public ?DateTimeImmutable $dateTime;
    public ?Collection $testCollection;
}
