<?php declare(strict_types=1);

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
     * @OA\Property(description="Todo identifier")
     * @Groups({"show_todo", "list_todos"})
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @OA\Property(type="string", maxLength=255, description="Todo title")
     * @Groups({"show_todo", "list_todos"})
     */
    private ?string $title = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @OA\Property(type="string", maxLength=255, description="Todo description", nullable=true)
     * @Groups({"show_todo"})
     */
    private ?string $description = null;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"show_todo", "list_todos"})
     */
    private ?bool $isComplete = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getIsComplete(): ?bool
    {
        return $this->isComplete;
    }

    public function setIsComplete(?bool $isComplete): void
    {
        $this->isComplete = $isComplete;
    }
}
