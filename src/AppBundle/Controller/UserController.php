<?php

namespace AppBundle\Controller;


use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use AppBundle\Service\Encryption\ArgonEncryption;
use AppBundle\Service\Users\UserServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
    /**
     * @var UserServiceInterface
     */
    private $userService;
    private $encryptionService;

    public function __construct(UserServiceInterface $userService, ArgonEncryption $encryptionService)
    {
        $this->userService = $userService;
        $this->encryptionService = $encryptionService;
    }

    /**
     * @Route("register", name="user_register", methods={"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
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
        $data = $request->request->get('user');
        if ($user->getEmail() !== $data['email'] && null !== $this->userService->findOneByEmail($data['email'])) {
            $messages[] = "This ( " . $data['email'] . " ) already token!";
            return $this->render('users/register.html.twig',
                ['form' => $this->createForm(UserType::class)->createView(), 'errors' => $messages]
            );
        }
        $form->handleRequest($request);
        $messages = $this->errorCollection($form, $messages);
        $user->setImage('');
        if ($form->isSubmitted() && $form->isValid()) {
            $count= $this->userService->allUser();
            $this->userService->save($user,$count);
            $this->addFlash('info', "You have successfully registered!");
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
        $allUser = $this->userService->allUser();
        $currentUser = $this->userService->currentUser();
        return $this->render('users/dashboard.html.twig', ['user' => $currentUser,'allUser'=>$allUser]);
    }

    /**
     * @Route("/dashboard/editProfife", name="edit_profile", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response|null
     */
    public function edit()
    {
        $currentUser = $this->userService->currentUser();

        $messages = [];
        return $this->render('users/edit.html.twig',
            [
                'user' => $currentUser,
                'form' => $this->createForm(UserType::class)->createView(),
                'errors' => $messages
            ]
        );
    }

    /**
     * @Route("/dashboard/editProfife", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return Response
     */
    public function editProcess(Request $request)
    {
        $messages = [];
        $currentUser = $this->userService->currentUser();
        $currentPassword = $currentUser->getPassword();
        $form = $this->createForm(UserType::class, $currentUser);
        $data = $request->request->get('user');
        if ($currentUser->getEmail() !== $data['email'] && null !== $this->userService->findOneByEmail($data['email'])) {
            $messages[] = "This ( " . $data['email'] . " ) already token!";
            return $this->render('users/edit.html.twig',
                [
                    'user' => $currentUser,
                    'form' => $this->createForm(UserType::class)->createView(),
                    'errors' => $messages
                ]
            );
        }

        $passwordHash = $this->checkPassword($request, $currentPassword, $currentUser);

        $form->handleRequest($request);

        $currentUser->setPassword($passwordHash);
        $messages = $this->errorCollection($form, $messages);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->uploadFile($form, $currentUser);
            $this->userService->update($currentUser);
            $this->addFlash('info', 'Update Profile successfully!');
            return $this->redirectToRoute("user_office");
        }
        return $this->render('users/edit.html.twig',
            [
                'user' => $currentUser,
                'form' => $this->createForm(UserType::class)->createView(),
                'errors' => $messages
            ]
        );
    }

    /**
     * @param FormInterface $form
     * @param $currentUser
     */
    private function uploadFile(FormInterface $form, $currentUser)
    {
        /**
         * @var UploadedFile $file
         */
        $file = $form['image']->getData();
        if ($file!==null) {
            $fs = new Filesystem();
            $path = $this->getParameter('user_image') . $currentUser->getImage();
//            if ($path){
//                $fs->remove($path);
//            }
            $fileName = md5(uniqid()) . "." . $file->guessExtension();
            $file->move(
                $this->getParameter('user_image'),
                $fileName
            );
            $currentUser->setImage($fileName);
        }
    }

    /**
     * @param FormInterface $form
     * @param array $messages
     * @return array
     */
    private function checkValidEmail(FormInterface $form, array $messages): array
    {
        $email = $this->userService->findOneByEmail($form['email']->getData())->getEmail();
        if (null !== $email) {
            $messages[] = "This ( {$email} ) already token!";
        }
        return $messages;
    }

    /**
     * @param Request $request
     * @param string $currentPassword
     * @param User|null $currentUser
     * @return false|string|null
     */
    private function checkPassword(Request $request, string $currentPassword, ?User $currentUser)
    {
        $data = $request->request->get('user');
        if ($data['password']['first'] == null) {
            $data['password']['first'] = $currentPassword;
            $data['password']['second'] = $currentPassword;
            $request->request->set('user', $data);
            $passwordHash = $currentPassword;
        } else {
            $passwordHash = $this->encryptionService->hash($currentUser->getPassword());
        }
        return $passwordHash;
    }

    /**
     * @param FormInterface $form
     * @param array $messages
     * @return array
     */
    private function errorCollection(FormInterface $form, array $messages): array
    {
        foreach ($form->getErrors(true) as $err) {
            $messages[] = $err->getMessage();
        }
        return $messages;
    }


}
