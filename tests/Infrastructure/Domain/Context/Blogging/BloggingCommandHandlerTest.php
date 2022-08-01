<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Domain\Context\Blogging;

use App\Domain\Context\Blogging\Command\UpdateBlog;
use App\Domain\Context\Blogging\BloggingCommandHandler;
use App\Domain\Context\Blogging\Command\CreateBlog;
use App\Domain\Context\Blogging\ValueObject\BlogIdentifier;
use App\Domain\Context\User\ValueObject\UserIdentifier;
use App\Infrastructure\EventSourcing\EventWithMetadata;
use Doctrine\ORM\EntityManager;
use Neos\EventStore\Model\EventStream\EventStreamInterface;
use ReflectionMethod;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\DoctrineReceiver;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\DoctrineTransport;
use Symfony\Component\Messenger\EventListener\StopWorkerOnMessageLimitListener;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\Serialization\Serializer;
use Symfony\Component\Messenger\Worker;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\Connection;

class BloggingCommandHandlerTest extends KernelTestCase
{

    private BloggingCommandHandler $bloggingCommandHandler;
    private EntityManager $entityManager;
    private EventDispatcherInterface $eventDispatcher;
    private MessageBusInterface $messageBus;
    private LoggerInterface $logger;

    public function setUp(): void
    {
        parent::setUp();

        $kernel = $this->bootKernel();
        $container = $kernel->getContainer();

        /** @var BloggingCommandHandler $bloggingCommandHandler */
        $bloggingCommandHandler = $container->get(BloggingCommandHandler::class);
        $this->bloggingCommandHandler = $bloggingCommandHandler;

        $messageBus = $container->get('messenger.default_bus');
        assert($messageBus instanceof MessageBusInterface);
        $this->messageBus = $messageBus;

        $logger = $container->get('monolog');
        assert($logger instanceof LoggerInterface);
        $this->logger = $logger;

        $eventDispatcher = $container->get(EventDispatcherInterface::class);
        $eventDispatcher->addSubscriber(new StopWorkerOnMessageLimitListener(1, $logger));
        /* @var EventDispatcherInterface $eventDispatcher */
        $this->eventDispatcher = $eventDispatcher;

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

        $this->runWorker();

        $query = "
            SELECT 
              name,     
              COUNT(*) AS NumberOfBlogs
            FROM blog
            GROUP BY name
        ";

        $result = $this->entityManager
            ->getConnection()
            ->executeQuery($query)
            ->fetchAllAssociative();

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
     *
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
            ->executeQuery($query)
            ->fetchAllAssociative();

        // then the blog reflection is updated too
        $this->assertEquals(
            'Minecraft 16.2',
            $result['name']
        );
    }

    /**
     *
     */
    public function handleStreamReturnsTheStream()
    {
        // given a event stream
        $blogIdentifier = BlogIdentifier::fromString($this->findBlogId());
        $stream = $this->bloggingCommandHandler->handleStream($blogIdentifier);
        /* @var $stream EventStreamInterface */

        // when we use the current stream
        $createEvent = $stream->getIterator()->current();
        assert($createEvent instanceof EventWithMetadata);

        var_dump($createEvent);
        die();
        #$createEventPayload = $createEvent->

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

    private function runWorker(): void
    {
        $this->doctrineTransport = new DoctrineTransport(
            new Connection([], $this->entityManager->getConnection()),
            new Serializer()
        );

        $receiver = $this->receiver();
        $worker = new Worker(
            [$receiver],
            $this->messageBus,
            $this->eventDispatcher,
            $this->logger
        );
        $worker->run();
    }

    public function receiver(): DoctrineReceiver
    {
        $method = new ReflectionMethod(DoctrineTransport::class, "getReceiver");
        $method->setAccessible(true);
        return $method->invoke($this->doctrineTransport);
    }
}
