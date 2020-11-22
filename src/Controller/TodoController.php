<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Exception\Todo\TodoNotFoundException;
use App\Service\TodoService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class TodoController
 * @package App\Controller
 * @Route("/api/todos")
 */
class TodoController extends AbstractController
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var TodoService
     */
    private $todoService;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(LoggerInterface $logger, TodoService $todoService, SerializerInterface $serializer)
    {

        $this->logger = $logger;
        $this->todoService = $todoService;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/", methods={"GET"}, name="todos_get")
     *
     * @OA\Response(
     *     response=200,
     *     description="A list with todos",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=Todo::class)),
     *         example={"id": 1, "title": "title", "description": "description", "isComplete": false}
     *     ),
     * )
     *
     * @return JsonResponse
     */
    public function getTodos(): JsonResponse
    {
        return $this->json($this->todoService->getTodos());
    }

    /**
     * @Route("/{id}", methods={"GET"}, name="todo_get")
     *
     * @OA\Response(
     *     response=200,
     *     description="A todo with given id",
     *     @OA\JsonContent(
     *         type="object",
     *         ref=@Model(type=Todo::class),
     *         example={"id": 1, "title": "title", "description": "description", "isComplete": false}
     *     )
     * )
     *
     * @OA\Response(response=404, description="Todo not found")
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getTodoById(int $id): JsonResponse
    {
        try {
            $todo = $this->todoService->getTodoById($id);
        } catch (TodoNotFoundException $exception) {
            $this->logger->error($exception->getMessage());

            return $this->json([], Response::HTTP_NOT_FOUND);
        }
        return $this->json($todo);
    }

    /**
     * @Route("/", methods={"POST"}, name="todos_add")
     *
     * @OA\RequestBody(
     *     required=true,
     *     description="Pass title, description & isComplete",
     *     @OA\JsonContent(
     *         required={"title", "description", "isComplete"},
     *         @OA\Property(property="title", type="string", example="title"),
     *         @OA\Property(property="description", type="string", example="description"),
     *         @OA\Property(property="isComplete", type="boolean", example=false)
     *     )
     * )
     *
     * @OA\Response(response="201", description="Successful created")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createTodo(Request $request): JsonResponse
    {
        /** @var Todo $todo */
        $todo = $this->serializer->deserialize($request->getContent(), Todo::class, 'json');

        $this->todoService->save($todo);

        return $this->json($todo, Response::HTTP_CREATED);
    }

    /**
     * @Route("/{id}", methods={"PUT"}, name="todo_update")
     *
     * @OA\RequestBody(
     *     description="Pass atleast one parametr to update it",
     *     @OA\JsonContent(
     *         @OA\Property(property="title", type="string", example="title"),
     *         @OA\Property(property="description", type="string", example="description"),
     *         @OA\Property(property="isComplete", type="boolean", example=false)
     *     )
     * )
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateTodo(Request $request, int $id): JsonResponse
    {
        try {
            /** @var Todo $data */
            $data = $this->serializer->deserialize($request->getContent(), Todo::class, 'json');
            $this->todoService->updateTodo($data, $id);

            return $this->json([], Response::HTTP_OK);

        } catch (TodoNotFoundException $exception) {
            return $this->json([], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @Route("/{id}", methods={"DELETE"}, name="todo_delete")
     *
     * @OA\Response(response=200, description="Todo is deleted")
     * @OA\Response(response=404, description="Todo not found or has deleted")
     *
     * @param int $id
     * @return JsonResponse
     */
    public function deleteTodoById(int $id): JsonResponse
    {
        try {
            $this->todoService->deleteTodoById($id);
        } catch (TodoNotFoundException $exception) {
            $this->logger->error($exception->getMessage());

            return $this->json([], Response::HTTP_NOT_FOUND);
        }

        return $this->json([], Response::HTTP_OK);
    }

}
