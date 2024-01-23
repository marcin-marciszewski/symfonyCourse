<?php

namespace App\Entity;


use DateTimeImmutable;
use DateTime;
use App\Entity\DateImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Type;
use App\Repository\TestEntityRepository;
use JMS\Serializer\Annotation\SerializedName;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class TestEntity
{
    // 'Y-m-d'
    #[Assert\Type(DateImmutable::class)]
    #[Type(DateImmutable::class)]
    #[SerializedName('dateIssue')]
    // #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    private DateTimeImmutable $dateIssue;

    #[Assert\Type(DateImmutable::class)]
    #[Type(DateImmutable::class)]
    #[SerializedName('dateExpire')]
    // #[Context([DateTimeNormalizer::FORMAT_KEY => '|Y-m-d'])]
    private DateImmutable $dateExpire;

    #[Assert\Type('integer')]
    #[Type('integer')]
    #[SerializedName('accountId')]
    private int $accountId;

    #[Assert\Type('boolean')]
    #[Type('boolean')]
    #[SerializedName('dateExpire')]
    private bool $indefinate;


    public function getDateIssue(): ?DateImmutable
    {
        return $this->dateIssue;
    }

    public function setDateIssue(DateImmutable $dateIssue): static
    {
        $this->dateIssue = $dateIssue;

        return $this;
    }

    public function getDateExpire(): ?DateImmutable
    {
        return $this->dateExpire;
    }

    public function setDateExpire(DateImmutable $dateExpire): static
    {
        $this->dateExpire = $dateExpire;

        return $this;
    }

    public function getAccountId(): ?int
    {
        return $this->accountId;
    }

    public function setAccountId(int $accountId): static
    {
        $this->accountId = $accountId;

        return $this;
    }

    public function isIndefinate(): ?bool
    {
        return $this->indefinate;
    }

    public function setIndefinate(bool $indefinate): static
    {
        $this->indefinate = $indefinate;

        return $this;
    }
}
