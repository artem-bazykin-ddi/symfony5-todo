<?php

namespace App\Entity;

use App\Repository\TodoRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TodoRepository::class)
 *
 * @OA\Schema(
 *     description="Todo model",
 *     title="Todo model",
 *     required={"title"}
 * )
 */
class Todo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @var int
     * @OA\Property(description="Todo identifier")
     * @Groups({"show_todo", "list_todos"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     * @OA\Property(type="string", maxLength=255, description="Todo title")
     * @Groups({"show_todo", "list_todos"})
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     * @OA\Property(type="string", maxLength=255, description="Todo description", nullable=true)
     * @Groups({"show_todo"})
     */
    private $description;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"show_todo", "list_todos"})
     */
    private $isComplete;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
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

    public function getIsComplete(): ?bool
    {
        return $this->isComplete;
    }

    public function setIsComplete(?bool $isComplete): self
    {
        $this->isComplete = $isComplete;

        return $this;
    }
}
