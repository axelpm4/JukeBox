<?php

namespace App\Document;

use DateTimeImmutable;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Field;
use Doctrine\ODM\MongoDB\Mapping\Annotations\GeneratedValue;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Id;

#[Document(collection: 'avis')]
final class Avis
{
    #[Id]
    #[GeneratedValue(strategy: 'AUTO')]
    private mixed $id = null;

    #[Field(type: 'string')]
    private string $authorEmail;

    #[Field(type: 'int')]
    private int $rating;

    #[Field(type: 'string')]
    private string $text;

    #[Field(type: 'date')]
    private ?DateTimeImmutable $createdAt = null;

    public function getId(): mixed
    {
        return $this->id;
    }

    public function getAuthorEmail(): string
    {
        return $this->authorEmail;
    }

    public function setAuthorEmail(string $authorEmail): self
    {
        $this->authorEmail = $authorEmail;

        return $this;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
