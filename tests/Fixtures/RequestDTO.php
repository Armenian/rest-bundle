<?php
declare(strict_types=1);

namespace DMP\RestBundle\Tests\Fixtures;


use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class RequestDTO
{
    /**
     * @var string
     * @Assert\NotBlank(message="BLANK_VALIDATION_MESSAGE")
     */
    public ?string $testStringField = null;
    /**
     * @var string
     * @Assert\Email(message="EMAIL_VALIDATION_MESSAGE {{ value }}")
     * @Assert\NotBlank()
     */
    public string $testEmailField;
    /**
     * @var RequestSubDTO
     * @Serializer\Type("DMP\RestBundle\Tests\Fixtures\RequestSubDTO")
     * @Assert\Type("DMP\RestBundle\Tests\Fixtures\RequestSubDTO")
     * @Assert\Valid()
     */
    public RequestSubDTO $testSubField;
    /**
     * @var array|RequestSubDTO[]
     * @Serializer\Type("array<DMP\RestBundle\Tests\Fixtures\RequestSubDTO>")
     * @Assert\Valid()
     */
    public array $testArrayField;
    public ?DateTimeImmutable $dateTime = null;
    /**
     * @var Collection|RequestSubDTO[]|null
     * @Serializer\Type("Collection<DMP\RestBundle\Tests\Fixtures\RequestSubDTO>")
     * @Assert\Valid()
     */
    public ?Collection $testCollection = null;
}
