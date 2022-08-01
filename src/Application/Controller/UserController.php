<?php

declare(strict_types=1);

namespace App\Application\Controller;

use App\Domain\Context\User\Command\CreateUser;
use App\Domain\Context\User\Command\UpdateUser;
use App\Domain\Context\User\UserCommandHandler;
use App\Domain\Context\User\ValueObject\UserIdentifier;
use App\Domain\Projection\User\Repository\UserRepository;
use App\Domain\Projection\User\UserFinder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(
        private readonly UserCommandHandler $userCommandHandler,
        private readonly UserFinder $userFinder,
        private readonly UserRepository $userRepository
    ) {
    }

    #[Route(path: '/user/list', name: 'user')]
    public function list(): Response
    {
        return $this->render('user-list.html.twig', [
            'users' => $this->userFinder->execute(),
        ]);
    }

    #[Route(path: '/user/create', name: 'user-create')]
    public function create(Request $request): RedirectResponse
    {
        $content = $request->get('form');

        $this->userCommandHandler->handleCreateUser(new CreateUser($content['name'], $content['mail']));
        return $this->redirect('/user/list');
    }

    #[Route(path: '/user/edit/{id}', name: 'user-edit')]
    public function edit(string $id): \Symfony\Component\HttpFoundation\Response
    {
        $userIdentifier = UserIdentifier::fromString($id);
        return $this->render('user-edit.html.twig', [
            'users' => $this->userFinder->execute(),
            'currentUser' => $this->userRepository->findById($userIdentifier),
        ]);
    }

    #[Route(path: '/user/update', name: 'user-update')]
    public function update(Request $request): RedirectResponse
    {
        $content = $request->get('form');

        $this->userCommandHandler->handleUpdateUser(
            new UpdateUser(UserIdentifier::fromString($content['id']), $content['name'], $content['mail'])
        );
        return $this->redirect('/user/list');
    }

    #[Route(path: '/user/id/{id}', name: 'user-stream')]
    public function userStream(string $id): \Symfony\Component\HttpFoundation\Response
    {
        $userIdentifier = UserIdentifier::fromString($id);

        return $this->render('user-stream.html.twig', [
            'currentUser' => $this->userRepository->findById($userIdentifier),
            'users' => $this->userFinder->execute(),
            'stream' => $this->userCommandHandler->handleStream($userIdentifier),
        ]);
    }
}
