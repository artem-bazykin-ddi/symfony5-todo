<?php


namespace App\Service;


use App\Entity\Todo;
use App\Exception\Todo\TodoInternalServerError;
use App\Exception\Todo\TodoNotFoundException;
use App\Repository\TodoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;

class TodoService
{
    private EntityManagerInterface $entityManager;
    private TodoRepository $todoRepository;

    public function __construct(EntityManagerInterface $entityManager, TodoRepository $todoRepository)
    {
        $this->entityManager = $entityManager;
        $this->todoRepository = $todoRepository;
    }

    public function getTodos(Request $request): array
    {
        $limit = $request->query->get('pageSize');
        $offset = $request->query->get('page');
        return $this->todoRepository->findBy([], [], $limit, $offset);
    }

    /**
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
     * @throws TodoNotFoundException|TodoInternalServerError
     */
    public function updateTodo(Todo $data, int $id): void
    {
        $todo = $this->getTodoById($id);
        $todo->setTitle($data->getTitle() ?? $todo->getTitle());
        $todo->setDescription($data->getDescription() ?? $todo->getDescription());
        $todo->setIsComplete($data->getIsComplete() ?? $todo->getIsComplete());

        $this->save($todo, false);
    }

    /**
     * @throws TodoInternalServerError
     */
    public function save(Todo $todo = null, bool $isPersist = true): void
    {
        try {
            if ($isPersist) {
                $this->entityManager->persist($todo);
            }
            $this->entityManager->flush();
        } catch (Exception $e) {
            throw new TodoInternalServerError($e->getMessage());
        }
    }

    /**
     * @throws TodoNotFoundException|TodoInternalServerError
     */
    public function deleteTodoById(int $id): void
    {
        $todo = $this->getTodoById($id);

        $this->entityManager->remove($todo);
        $this->save(null, false);
    }
}
