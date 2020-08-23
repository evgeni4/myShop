<?php

namespace AppBundle\Controller;

use AppBundle\Service\Users\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ExceptionController extends Controller
{
    private $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function showAction()
    {
        $currentUser = $this->userService->currentUser();
        return $this->render('error/template.html.twig', ['user' => $currentUser]);
    }
}
