<?php

declare(strict_types=1);

namespace App\Tests\Domain\Context\Blogging;

use App\Domain\Context\Blogging\BloggingCommandHandler;
use App\Domain\Context\Blogging\Command\CreateBlog;
use App\Domain\Projection\User\UserIdentifier;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BloggingCommandHandlerTest extends KernelTestCase
{
    /**
     * @var BloggingCommandHandler
     */
    private $bloggingCommandHandler;

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function setUp()
    {
        parent::setUp();

        $kernel = $this->bootKernel();
        $container = $kernel->getContainer();

        $this->bloggingCommandHandler = $container->get(BloggingCommandHandler::class);
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * @test
     */
    public function handleCreateBlogReturnsTheExpectedResult()
    {
        // given a create blog command
        $command = new CreateBlog(
            'Minecraft',
            UserIdentifier::fromString('Mr. Author')
        );

        // when the event is stored
        $this->bloggingCommandHandler->handleCreateBlog($command);

        $query = "
            SELECT 
              name,     
              COUNT(*) AS NumberOfBlogs
            FROM blog
            GROUP BY name
        ";

        $result = $this->entityManager
            ->getConnection()
            ->query($query)
            ->fetch();

        // then the given name is returned
        $this->assertEquals(
            1,
            $result['NumberOfBlogs']
        );

        $this->assertEquals(
            'Minecraft',
            $result['name']
        );
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $query = "
            Truncate table blog;
            Truncate table blog_events;
            Truncate table neos_eventsourcing_eventlistener_appliedeventslog
        ";
        $this->entityManager->getConnection()->exec($query);
    }
}
