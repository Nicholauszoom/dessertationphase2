<?php

namespace App\Entity;

use App\Repository\IndexDetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IndexDetailRepository::class)]
class IndexDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    // #[ORM\Column(length: 255)]
    // private ?string $imagePath = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    // public function getImagePath(): ?string
    // {
    //     return $this->imagePath;
    // }

    // public function setImagePath(string $imagePath): self
    // {
    //     $this->imagePath = $imagePath;

    //     return $this;
    // }
}
