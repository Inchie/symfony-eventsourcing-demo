<?php

declare(strict_types=1);

namespace App\Controller;

use App\Domain\Context\Blog\Command\UpdateBlog;
use App\Domain\Context\Blogging\BloggingCommandHandler;
use App\Domain\Context\Blogging\Command\CreateBlog;
use App\Domain\Projection\Blog\BlogFinder;
use App\Domain\Projection\Blog\BlogIdentifier;
use App\Domain\Projection\Blog\Infrastructure\DoctrineBlogRepository;
use App\Domain\Projection\User\Repository\UserRepository;
use App\Domain\Projection\User\UserIdentifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    private $userRepository;
    private $bloggingCommandHandler;
    private $blogRepository;
    private $blogFinder;

    public function __construct(
        UserRepository $userRepository,
        BloggingCommandHandler $bloggingCommandHandler,
        DoctrineBlogRepository $blogRepository,
        BlogFinder $blogFinder
    ) {
        $this->userRepository = $userRepository;
        $this->bloggingCommandHandler = $bloggingCommandHandler;
        $this->blogRepository = $blogRepository;
        $this->blogFinder = $blogFinder;
    }

    /**
     * @Route("/blog/list", name="blog")
     */
    public function list(): Response
    {
        return $this->render('blog-list.html.twig', [
            'users' => $this->userRepository->findAll(),
            'blogs' => $this->blogRepository->findAll()
        ]);
    }

    /**
    * @Route("/blog/create", name="blog-create")
    */
    public function create(Request $request): RedirectResponse
    {
        $this->bloggingCommandHandler->handleCreateBlog(
            new CreateBlog(
                $request->request->get('form')['name'],
                UserIdentifier::fromString($request->request->get('form')['author'])
            )
        );

        return $this->redirect('/blog/list');
    }

    /**
     * @Route("/blog/edit/{id}", name="blog-edit")
     */
    public function edit(string $id)
    {
        $blogIdentifier = BlogIdentifier::fromString($id);
        return $this->render('blog-edit.html.twig', [
            'currentBlog' => $this->blogRepository->findById($blogIdentifier),
            'blogs' => $this->blogRepository->findAll()
        ]);
    }

    /**
     * @Route("/blog/update", name="blog-update")
     */
    public function update(Request $request): RedirectResponse
    {
        $this->bloggingCommandHandler->handleUpdateBlog(
            new UpdateBlog(
                BlogIdentifier::fromString($request->request->get('form')['id']),
                $request->request->get('form')['name']
            )
        );

        return $this->redirect('/blog/list');
    }

    /**
     * @Route("/blog/show/{id}", name="blog-show")
     */
    public function show(string $id)
    {
        $blogIdentifier = BlogIdentifier::fromString($id);
        return $this->render('blog-show.html.twig', [
            'users' => $this->userRepository->findAll(),
            'blog' => $this->blogFinder->execute($blogIdentifier),
            'stream' => $this->bloggingCommandHandler->handleStream($blogIdentifier),
        ]);
    }
}
