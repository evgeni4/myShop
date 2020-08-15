<?php

namespace AppBundle\Controller;


use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use AppBundle\Service\Users\UserServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
            ['form' => $this->createForm(UserType::class)->createView(), 'errors' => $messages]
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
            ['form' => $this->createForm(UserType::class)->createView(), 'errors' => $messages]
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

    /**
     * @Route("/dashboard", name="user_office")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response|null
     */
    public function dashboard()
    {

        $currentUser = $this->userService->currentUser();
        return $this->render('users/dashboard.html.twig', ['user' => $currentUser]);
    }

    /**
     * @Route("/dashboard/edit", name="edit_profile", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response|null
     */
    public function edit()
    {
        $currentUser = $this->userService->currentUser();
        return $this->render('users/edit.html.twig',
            [
                'user' => $currentUser,
                'form' => $this->createForm(UserType::class)->createView()
            ]
        );
    }

    /**
     * @Route("/dashboard/edit", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return Response
     */
    public function editProcess(Request $request)
    {
        $currentUser = $this->userService->currentUser();
        $form = $this->createForm(UserType::class, $currentUser);
        if ($currentUser->getEmail() === $request->request->get('email')) {
            $form->remove('email');
            $form->remove('password');
        }
//        if ($request->request->get('image') == null) {
//            $form->remove('image');
//        }
        $form->handleRequest($request);
        $this->uploadFile($form, $currentUser);
        $this->userService->update($currentUser);
        $this->addFlash('info','Update Profile successfully!');
        return $this->redirectToRoute("user_office");
    }

    /**
     * @param FormInterface $form
     * @param User $user
     */
    private function uploadFile(FormInterface $form, User $user)
    {
        /**
         * @var UploadedFile $file
         */
       $file = $form['image']->getData();
        $fileName = md5(uniqid()) . "." . $file->guessExtension();
        if ($file) {
            $file->move(
                $this->getParameter('user_image'),
                $fileName
            );
        }
        $user->setImage($fileName);
    }
}
