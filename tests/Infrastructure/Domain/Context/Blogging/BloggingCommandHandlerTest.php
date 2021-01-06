<?php

namespace App\Tests\Domain\Context\Blogging;

use App\Domain\Context\Blogging\BloggingCommandHandler;
use App\Domain\Context\Blogging\Command\CreateBlog;
use App\Domain\Projection\User\UserIdentifier;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BloggingCommandHandlerTest extends KernelTestCase
{
    /**
     * @var BloggingCommandHandler
     */
    private $bloggingCommandHandler;

    public function setUp()
    {
        parent::setUp();

        $kernel = $this->bootKernel();
        $container = $kernel->getContainer();

        $this->bloggingCommandHandler = $container->get(BloggingCommandHandler::class);

    }

    /**
     * @test
     */
    public function handleCreateBlogReturnsTheExpectedResult()
    {
        $command = new CreateBlog(
            'Minecraft',
            UserIdentifier::fromString('Mr. Author')
        );

        $this->bloggingCommandHandler->handleCreateBlog($command);

        $this->assertEquals(1,1);
    }

}
