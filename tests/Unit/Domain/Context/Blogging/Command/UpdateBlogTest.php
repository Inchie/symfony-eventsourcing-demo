<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Context\Blogging\Command;

use App\Domain\Context\Blogging\Command\UpdateBlog;
use App\Domain\Context\Blogging\ValueObject\BlogIdentifier;
use PHPUnit\Framework\TestCase;

class UpdateBlogTest extends TestCase
{
    /**
     * @test
     */
    public function createBlogReturnsTheExpectedBlog()
    {
        $updateBlog = new UpdateBlog(
            BlogIdentifier::fromString('ABC-DEF'),
            'Minecraft 16.2'
        );

        $this->assertEquals(
            'Minecraft 16.2',
            $updateBlog->getName()
        );

        $this->assertEquals(
            'ABC-DEF',
            $updateBlog->getId()->jsonSerialize()
        );
    }
}
