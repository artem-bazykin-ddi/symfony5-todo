<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Todo;
use App\Exception\Todo\TodoInternalServerError;
use App\Exception\Todo\TodoNotFoundException;
use App\Service\TodoService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api/todos", name="todos_")
 */
class TodoController extends AbstractController
{
    private LoggerInterface $logger;
    private TodoService $todoService;
    private SerializerInterface $serializer;

    public function __construct(LoggerInterface $logger, TodoService $todoService, SerializerInterface $serializer)
    {
        $this->logger = $logger;
        $this->todoService = $todoService;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/", methods={"GET"}, name="get_all")
     *
     * @OA\Parameter(
     *     name="pageSize",
     *     in="query",
     *     description="Number of todo per page"
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="Current page"
     * )
     * @OA\Response(
     *     response=200,
     *     description="A list with todos",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=Todo::class)),
     *         example={"id": 1, "title": "title", "description": "description", "isComplete": false}
     *     ),
     * )
     */
    public function getTodos(Request $request): Response
    {
        return $this->json($this->todoService->getTodos($request));
    }

    /**
     * @Route("/{id}", methods={"GET"}, name="show")
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
     */
    public function show(Todo $todo = null): Response
    {
        $status = Response::HTTP_OK;
        if (empty($todo)) {
            $status = Response::HTTP_NOT_FOUND;
        }
        return $this->json($todo, $status);
    }

    /**
     * @Route("/", methods={"POST"}, name="add")
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
     * @OA\Response(response="201", description="Successful created")
     * @OA\Response(response="500", description="Internal server error")
     */
    public function createTodo(Request $request): Response
    {
        $status = Response::HTTP_CREATED;
        try {
            /** @var Todo $todo */
            $todo = $this->serializer->deserialize($request->getContent(), Todo::class, 'json');
            $this->todoService->save($todo);
        } catch (TodoInternalServerError $exception) {
            $this->logger->error($exception->getMessage());
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
            $todo = [];
        }

        return $this->json($todo, $status);
    }

    /**
     * @Route("/{id}", methods={"PUT"}, name="update")
     *
     * @OA\RequestBody(
     *     description="Pass atleast one parametr to update it",
     *     @OA\JsonContent(
     *         @OA\Property(property="title", type="string", example="title"),
     *         @OA\Property(property="description", type="string", example="description"),
     *         @OA\Property(property="isComplete", type="boolean", example=false)
     *     )
     * )
     * @OA\Response(response="200", description="Successful updated")
     * @OA\Response(response=404, description="Todo not found")
     */
    public function updateTodo(Request $request, int $id): Response
    {
        $status = Response::HTTP_OK;
        try {
            /** @var Todo $data */
            $data = $this->serializer->deserialize($request->getContent(), Todo::class, 'json');
            $this->todoService->updateTodo($data, $id);
        } catch (TodoNotFoundException $exception) {
            $this->logger->error($exception->getMessage());
            $status = Response::HTTP_NOT_FOUND;
        }

        return $this->json([], $status);
    }

    /**
     * @Route("/{id}", methods={"DELETE"}, name="delete")
     *
     * @OA\Response(response=200, description="Todo is deleted")
     * @OA\Response(response=404, description="Todo not found or has been deleted")
     */
    public function deleteTodoById(int $id): Response
    {
        $status = Response::HTTP_OK;
        try {
            $this->todoService->deleteTodoById($id);
        } catch (TodoNotFoundException $exception) {
            $this->logger->error($exception->getMessage());
            $status = Response::HTTP_NOT_FOUND;
        }

        return $this->json([], $status);
    }
}
