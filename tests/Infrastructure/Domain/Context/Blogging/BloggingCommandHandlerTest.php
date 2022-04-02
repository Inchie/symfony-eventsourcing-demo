<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Domain\Context\Blogging;

use App\Domain\Context\Blogging\Command\UpdateBlog;
use App\Domain\Context\Blogging\BloggingCommandHandler;
use App\Domain\Context\Blogging\Command\CreateBlog;
use App\Domain\Projection\Blog\BlogIdentifier;
use App\Domain\Projection\User\UserIdentifier;
use Doctrine\ORM\EntityManager;
use Neos\EventSourcing\EventStore\EventStream;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BloggingCommandHandlerTest extends KernelTestCase
{
    /**
     * @var BloggingCommandHandler
     */
    private BloggingCommandHandler $bloggingCommandHandler;

    /**
     * @var EntityManager
     */
    private EntityManager $entityManager;

    public function setUp(): void
    {
        parent::setUp();

        $kernel = $this->bootKernel();
        $container = $kernel->getContainer();

        /** @var BloggingCommandHandler $bloggingCommandHandler */
        $bloggingCommandHandler = $container->get(BloggingCommandHandler::class);
        $this->bloggingCommandHandler = $bloggingCommandHandler;

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * @test
     */
    public function handleCreateBlogCreatesTheBlog()
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

        // then the blog reflection is created too
        $this->assertEquals(
            1,
            $result['NumberOfBlogs']
        );

        $this->assertEquals(
            'Minecraft',
            $result['name']
        );
    }

    /**
     * @test
     */
    public function handleUpdateBlogUpdatesTheBlog()
    {
        // given a update blog command
        $command = new UpdateBlog(
            BlogIdentifier::fromString($this->findBlogId()),
            'Minecraft 16.2'
        );

        // when the update event is stored
        $this->bloggingCommandHandler->handleUpdateBlog($command);

        $query = "
            SELECT 
              name
            FROM blog
        ";

        $result = $this->entityManager
            ->getConnection()
            ->query($query)
            ->fetch();

        // then the blog reflection is updated too
        $this->assertEquals(
            'Minecraft 16.2',
            $result['name']
        );
    }

    /**
     * @test
     */
    public function handleStreamReturnsTheStream()
    {
        // given a event stream
        $blogIdentifier = BlogIdentifier::fromString($this->findBlogId());
        $stream = $this->bloggingCommandHandler->handleStream($blogIdentifier);
        /* @var $stream EventStream */

        // when we use the current stream
        $createEvent = $stream->current();
        $createEventPayload = $createEvent->getRawEvent()->getPayload();

        // then the payload is as expected
        $this->assertEquals(
            'Minecraft',
            $createEventPayload['name']
        );

        $this->assertEquals(
            'Mr. Author',
            $createEventPayload['author']
        );

        // when we use the next stream
        $stream->next();

        $updateEvent = $stream->current();
        $updateEventPayload = $updateEvent->getRawEvent()->getPayload();

        // then the payload is also as expected
        $this->assertEquals(
            'Minecraft 16.2',
            $updateEventPayload['name']
        );

        $this->truncateDatabase();
    }

    private function findBlogId()
    {
        $query = "
            SELECT 
              id
            FROM blog
        ";

        $result = $this->entityManager
            ->getConnection()
            ->query($query)
            ->fetch();

        return $result['id'];
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function truncateDatabase()
    {
        $query = "
            Truncate table blog;
            Truncate table blog_events;
            Truncate table neos_eventsourcing_eventlistener_appliedeventslog
        ";
        $this->entityManager->getConnection()->exec($query);
    }
}
