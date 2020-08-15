<?php

namespace AppBundle\Controller;


use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use AppBundle\Service\Users\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
    /**
     * @var UserServiceInterface
     */
    private $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }
    /**
     * @Route("register", name="user_register", methods={"GET"})
     *
     * @return Response
     */
    public function register()
    {
        $messages = [];
        return $this->render('users/register.html.twig',
            ['form' => $this->createForm(UserType::class)->createView(),'errors'=>$messages]
        );
    }
    /**
     * @Route("register", methods={"POST"})
     * @param Request $request
     * @return Response|null
     */
    public function registerProcess(Request $request)
    {
       $messages = [];
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        foreach ($form->getErrors(true) as $err) {
            $messages[] = $err->getMessage();
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->save($user);
           return $this->redirectToRoute('security_login');
        }
        return $this->render('users/register.html.twig',
            [ 'form' => $this->createForm(UserType::class)->createView(), 'errors' => $messages]
        );
    }
    /**
     * @Route("logout",name="security_logout")
     * @throws Exception
     */
    public function logout()
    {
        throw new Exception("Logout failed");
    }
}
