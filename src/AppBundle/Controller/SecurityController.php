<?php

namespace AppBundle\Controller;

use AppBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="security_login")
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response|null
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        return $this->render('security/login.html.twig',
            [
                'form' => $this->createForm(UserType::class)->createView(),
                'error'  => $error
            ]
        );
    }
}
