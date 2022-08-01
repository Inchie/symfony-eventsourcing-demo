<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Projection\Blog\Infrastructure;

use App\Domain\Context\Blogging\Event\BlogWasCreated;
use App\Domain\Context\Blogging\ValueObject\BlogIdentifier;
use App\Domain\Context\User\ValueObject\UserIdentifier;
use App\Domain\Projection\Blog\Infrastructure\DoctrineBlogRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DoctrineBlogRepositoryTest extends KernelTestCase
{
    /**
     * @var DoctrineBlogRepository
     */
    private $blogRepository;

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function setUp(): void
    {
        parent::setUp();

        $kernel = $this->bootKernel();
        $container = $kernel->getContainer();

        $this->blogRepository = $container->get(DoctrineBlogRepository::class);
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * @test
     */
    public function addByEventBlogReturnsTheExpectedResult()
    {
        // given a event
        $blogWasCreated = new BlogWasCreated(
            BlogIdentifier::fromString('ABC'),
            'Minecraft',
            UserIdentifier::fromString('DEF')
        );

        // when the repository store this event
        $this->blogRepository->addByEvent($blogWasCreated);

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
        ";
        $this->entityManager->getConnection()->exec($query);
    }
}
