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
        $title = "Error Page";
        return $this->render('error/template.html.twig', ['titlePage'=>$title,'user' => $currentUser]);
    }
}
