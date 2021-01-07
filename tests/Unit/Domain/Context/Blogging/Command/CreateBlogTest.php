<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Context\Blogging\Command;

use App\Domain\Context\Blogging\Command\CreateBlog;
use App\Domain\Projection\User\UserIdentifier;
use PHPUnit\Framework\TestCase;

class CreateBlogTest extends TestCase
{
    /**
     * @test
     */
    public function createBlogCommandReturnsTheExpectedBlog()
    {
        $newBlog = new CreateBlog(
            'Minecraft',
            UserIdentifier::fromString('ABC-DEF')
        );

        $this->assertEquals(
            'Minecraft',
            $newBlog->getName()
        );

        $this->assertEquals(
            'ABC-DEF',
            $newBlog->getAuthorIdentifier()->jsonSerialize()
        );
    }
}
