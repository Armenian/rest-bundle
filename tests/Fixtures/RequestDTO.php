<?php
declare(strict_types=1);

namespace DMP\RestBundle\Tests\Fixtures;


use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @Assert\Type("DMP\RestBundle\Tests\Fixtures\RequestSubDTO")
     * @Assert\Valid()
     */
    public RequestSubDTO $testSubField;

    /**
     * @var array<RequestSubDTO>
     * @Assert\Valid()
     */
    public array $testArrayField;

    public ?DateTimeImmutable $dateTime = null;

    /**
     * @var ArrayCollection<RequestSubDTO>|null
     * @Assert\Valid()
     */
    public ?ArrayCollection $testCollection = null;

    public function setTestCollection(?array $testCollection): void
    {
        $this->testCollection = new ArrayCollection($testCollection);
    }
}
