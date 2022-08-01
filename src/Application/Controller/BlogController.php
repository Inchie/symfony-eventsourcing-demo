<?php

declare(strict_types=1);

namespace App\Application\Controller;

use App\Domain\Context\Blogging\BloggingCommandHandler;
use App\Domain\Context\Blogging\Command\CreateBlog;
use App\Domain\Context\Blogging\Command\UpdateBlog;
use App\Domain\Context\Blogging\ValueObject\BlogIdentifier;
use App\Domain\Context\User\ValueObject\UserIdentifier;
use App\Domain\Projection\Blog\BlogFinder;

use App\Domain\Projection\Blog\Infrastructure\DoctrineBlogRepository;
use App\Domain\Projection\User\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly BloggingCommandHandler $bloggingCommandHandler,
        private readonly DoctrineBlogRepository $blogRepository,
        private readonly BlogFinder $blogFinder
    ) {
    }

    #[Route(path: '/blog/list', name: 'blog')]
    public function list(): Response
    {
        return $this->render('blog-list.html.twig', [
            'users' => $this->userRepository->findAll(),
            'blogs' => $this->blogRepository->findAll(),
        ]);
    }

    #[Route(path: '/blog/create', name: 'blog-create')]
    public function create(Request $request): RedirectResponse
    {
        $content = $request->get('form');

        $this->bloggingCommandHandler->handleCreateBlog(
            new CreateBlog($content['name'], UserIdentifier::fromString($content['author']))
        );
        return $this->redirect('/blog/list');
    }

    #[Route(path: '/blog/edit/{id}', name: 'blog-edit')]
    public function edit(string $id): \Symfony\Component\HttpFoundation\Response
    {
        $blogIdentifier = BlogIdentifier::fromString($id);
        return $this->render('blog-edit.html.twig', [
            'currentBlog' => $this->blogRepository->findById($blogIdentifier),
            'blogs' => $this->blogRepository->findAll(),
        ]);
    }

    #[Route(path: '/blog/update', name: 'blog-update')]
    public function update(Request $request): RedirectResponse
    {
        $content = $request->get('form');

        $this->bloggingCommandHandler->handleUpdateBlog(
            new UpdateBlog(BlogIdentifier::fromString($content['id']), $content['name'])
        );
        return $this->redirect('/blog/list');
    }

    #[Route(path: '/blog/show/{id}', name: 'blog-show')]
    public function show(string $id): \Symfony\Component\HttpFoundation\Response
    {
        $blogIdentifier = BlogIdentifier::fromString($id);
        return $this->render('blog-show.html.twig', [
            'users' => $this->userRepository->findAll(),
            'blog' => $this->blogFinder->execute($blogIdentifier),
            'stream' => $this->bloggingCommandHandler->handleStream($blogIdentifier),
        ]);
    }
}
