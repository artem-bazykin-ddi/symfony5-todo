<?php


namespace App\DataFixtures;


use App\Entity\Todo;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TodoFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $todos = [
            [
                'title' => 'Test title 1',
                'description' => 'Test description 1',
                'isComplete' => false
            ],
            [
                'title' => 'Test title 2',
                'description' => 'Test description 2',
                'isComplete' => true
            ],
            [
                'title' => 'Test title 3',
                'description' => 'Test description 3',
                'isComplete' => false
            ]
        ];

        foreach ($todos as $item) {
            $todo = new Todo();
            $todo->setTitle($item['title']);
            $todo->setDescription($item['description']);
            $todo->setIsComplete($item['isComplete']);
            $manager->persist($todo);
        }
        $manager->flush();
    }
}
