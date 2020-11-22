<?php


namespace App\Service;


use App\Entity\Todo;
use App\Exception\Todo\TodoNotFoundException;
use App\Repository\TodoRepository;
use Doctrine\ORM\EntityManagerInterface;

final class TodoService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var TodoRepository
     */
    private $todoRepository;

    public function __construct(EntityManagerInterface $entityManager, TodoRepository $todoRepository)
    {
        $this->entityManager = $entityManager;
        $this->todoRepository = $todoRepository;
    }

    /**
     * @return array
     */
    public function getTodos(): array
    {
        return $this->todoRepository->findAll();
    }

    /**
     * @param int $id
     * @return Todo
     *
     * @throws TodoNotFoundException
     */
    public function getTodoById(int $id): Todo
    {
        $todo = $this->todoRepository->find($id);
        if (empty($todo)) {
            throw new TodoNotFoundException('Todo not found');
        }

        return $todo;
    }

    /**
     * @param Todo $data
     * @param int $id
     * @throws TodoNotFoundException
     */
    public function updateTodo(Todo $data, int $id): void
    {
        $todo = $this->getTodoById($id);
        $todo->setTitle($data->getTitle() ?? $todo->getTitle());
        $todo->setDescription($data->getDescription() ?? $todo->getDescription());
        $todo->setIsComplete($data->getIsComplete() ?? $todo->getIsComplete());

        $this->save($todo);
    }

    /**
     * @param Todo $todo
     * @return $this
     */
    public function save(Todo $todo): self
    {
        $this->entityManager->persist($todo);
        $this->entityManager->flush();

        return $this;
    }

    /**
     * @param int $id
     *
     * @throws TodoNotFoundException
     */
    public function deleteTodoById(int $id): void
    {
        $todo = $this->getTodoById($id);

        $this->entityManager->remove($todo);
        $this->entityManager->flush();
    }
}
