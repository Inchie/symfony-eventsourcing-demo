<?php

declare(strict_types=1);

namespace App\Tests\Unit\Projection\Blog;

use App\Domain\Projection\Blog\Blog;
use PHPUnit\Framework\TestCase;

class BlogTest extends TestCase
{
    /**
     * @test
     */
    public function createBlogReturnsTheExpectedBlog()
    {
        $newBlog = Blog::create(
            'ABC-DEF',
            'Minecraft',
            'Mr. Author'
        );

        $this->assertEquals(
            'ABC-DEF',
            $newBlog->getId()
        );

        $this->assertEquals(
            'Minecraft',
            $newBlog->getName()
        );

        $this->assertEquals(
            'Mr. Author',
            $newBlog->getAuthor()
        );
    }
}
