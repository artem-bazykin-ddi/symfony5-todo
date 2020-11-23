<?php

namespace App\Tests;

use App\DataFixtures\TodoFixtures;
use App\Entity\Todo;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TodoControllerTest extends WebTestCase
{
    private $client;
    private EntityManager $manager;
    private ORMExecutor $executor;

    public function setUp()
    {
        $this->client = static::createClient();

        $this->manager = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->executor = new ORMExecutor($this->manager, new ORMPurger());

        // Run the schema update tool using our entity metadata
        $schemaTool = new SchemaTool($this->manager);
        $schemaTool->updateSchema($this->manager->getMetadataFactory()->getAllMetadata());
    }

    public function tearDown(): void
    {
        parent::tearDown();
        (new SchemaTool($this->manager))->dropDatabase();
    }

    protected function loadFixture($fixture)
    {
        $loader = new Loader();
        $fixtures = is_array($fixture) ? $fixture : [$fixture];
        foreach ($fixtures as $item) {
            $loader->addFixture($item);
        }
        $this->executor->execute($loader->getFixtures());
    }

    public function testTodoNotFound()
    {
        $this->client->request('GET', '/api/todos/1');
        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testListTodos()
    {
        $this->loadFixture(new TodoFixtures());
        $this->client->request('GET', '/api/todos/');

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($response->getContent(), json_encode([
            ['id' => 1, 'title' => 'Test title 1', 'description' => 'Test description 1', 'isComplete' => false],
            ['id' => 2, 'title' => 'Test title 2', 'description' => 'Test description 2', 'isComplete' => true],
            ['id' => 3, 'title' => 'Test title 3', 'description' => 'Test description 3', 'isComplete' => false],
        ]));
    }

    public function testAddTodo()
    {
        $this->loadFixture(new TodoFixtures());
        $this->client->request('POST', '/api/todos/', [], [], [], json_encode([
            'title' => 'Test title 1',
            'description' => 'Test description 1',
            'isComplete' => false
        ]));

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $em = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        $todos = $em->getRepository(Todo::class)->findAll();
        $this->assertCount(4, $todos);
    }

    public function testShowTodo()
    {
        $this->loadFixture(new TodoFixtures());
        $this->client->request('GET', '/api/todos/1');

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($response->getContent(), json_encode(
            ['id' => 1, 'title' => 'Test title 1', 'description' => 'Test description 1', 'isComplete' => false]
        ));
    }

    public function testDeleteTodo()
    {
        $this->loadFixture(new TodoFixtures());
        $this->client->request('DELETE', '/api/todos/1');

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($response->getContent(), json_encode([]));

        $em = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        $todos = $em->getRepository(Todo::class)->findAll();
        $this->assertCount(2, $todos);
    }
}
