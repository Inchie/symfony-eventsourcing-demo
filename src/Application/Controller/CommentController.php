<?php

declare(strict_types=1);

namespace App\Application\Controller;

use App\Domain\Context\Blogging\BloggingCommandHandler;
use App\Domain\Context\Blogging\Command\CreateComment;
use App\Domain\Context\Blogging\ValueObject\BlogIdentifier;
use App\Domain\Context\User\ValueObject\UserIdentifier;
use App\Domain\Projection\Blog\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    public function __construct(
        private readonly BloggingCommandHandler $bloggingCommandHandler,
        private readonly CommentRepository $commentRepository
    ) {
    }

    #[Route(path: '/comment/list', name: 'comment')]
    public function list(): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render('comment-list.html.twig', [
            'comments' => $this->commentRepository->findAll(),
        ]);
    }

    #[Route(path: '/comment/create', name: 'comment-create')]
    public function create(Request $request): RedirectResponse
    {
        $content = $request->get('form');

        $blogIdentifier = BlogIdentifier::fromString($content['blog']);
        $userIdentifier = UserIdentifier::fromString($content['user']);
        $this->bloggingCommandHandler->handleCreateComment(
            new CreateComment($blogIdentifier, $userIdentifier, $content['comment'])
        );
        $url = sprintf('/blog/show/%s', $blogIdentifier->jsonSerialize());
        return $this->redirect($url);
    }
}
