<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Projection\Blog;

use App\Domain\Context\Blogging\Event\BlogWasUpdated;
use App\Domain\Context\Blogging\Event\BlogWasCreated;
use App\Domain\Context\Blogging\ValueObject\BlogIdentifier;
use App\Domain\Context\User\ValueObject\UserIdentifier;
use App\Domain\Projection\Blog\BlogProjection;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BlogProjectorTest extends KernelTestCase
{
    /**
     * @var BlogProjection
     */
    private $blogProjector;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var RawEvent
     */
    private $rawEvent;

    public function setUp(): void
    {
        parent::setUp();

        $kernel = $this->bootKernel();
        $container = $kernel->getContainer();

        $this->blogProjector = $container->get(BlogProjection::class);
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->rawEvent = new RawEvent(
            1,
            'FooEventType',
            [],
            [],
            StreamName::fromString('ABC'),
            1,
            'ABC',
            new \DateTimeImmutable()
        );
    }

    /**
     * @test
     */
    public function getSubscribedEventsReturnsTheExpectedEvents()
    {
        $subscribedEvents = BlogProjection::getSubscribedEvents();

        $this->assertEquals(
            [
                BlogWasCreated::class => ['whenBlogWasCreated'],
                BlogWasUpdated::class => ['whenBlogWasUpdated']
            ],
            $subscribedEvents
        );
    }

    /**
     * @test
     */
    public function blogWasCreatedEventStoreTheBlog()
    {
        // given a event
        $blogWasCreated = new BlogWasCreated(
            BlogIdentifier::fromString('ABC'),
            'Minecraft',
            UserIdentifier::fromString('DEF')
        );

        // when the projector stored the event
        $this->blogProjector->whenBlogWasCreated($blogWasCreated, $this->rawEvent);

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

        // then the event is stored
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
        ";
        $this->entityManager->getConnection()->exec($query);
    }
}
