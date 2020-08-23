<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Metals;
use AppBundle\Form\MetalsType;
use AppBundle\Service\Metals\MetalsService;
use AppBundle\Service\Users\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MetalsController extends Controller
{
    private $metalsService;
    private $userService;

    /**
     * MetalsController constructor.
     * @param $metalsService
     * @param $userService
     */
    public function __construct(MetalsService $metalsService, UserService $userService)
    {
        $this->metalsService = $metalsService;
        $this->userService = $userService;
    }

    /**
     * @Route("/dashboard/metals", name="all_metals", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response|null
     */
    public function metals()
    {
        $currentUser = $this->userService->currentUser();
        if ($currentUser->isUser()) {
            return $this->redirectToRoute('shop_index');
        }
        $metals = $this->metalsService->getAllMetals();
        return $this->render('metals/all_metals.html.twig',
            [
                'user' => $currentUser,
                'metals' => $metals,
            ]
        );
    }

    /**
     * @Route("/dashboard/metals/add", name="add_metal", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function addMetal()
    {
        $messages = [];
        $currentUser = $this->userService->currentUser();
        if ($currentUser->isUser()) {
            return $this->redirectToRoute('shop_index');
        }
        return $this->render('metals/add_metals.html.twig',
            [
                'errors' => $messages,
                'user' => $currentUser,
                'form' => $this->createForm(MetalsType::class)->createView()
            ]);
    }

    /**
     * @Route("/dashboard/metals/add", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return RedirectResponse|Response|null
     */
    public function addMetalProcess(Request $request)
    {
        $messages = [];
        $currentUser = $this->userService->currentUser();

        $metal = new Metals();
        $form = $this->createForm(MetalsType::class, $metal);
        $form->handleRequest($request);
        $messages = $this->errorCollection($form, $messages);
        $data = $request->request->get('metals');
        if ($currentUser->isUser()) {
            return $this->redirectToRoute('shop_index');
        }
        if (null !== $this->metalsService->getTitle($data['title'])) {
            $messages[] = "This Title ( " . $data['title'] . " ) already token!";
            return $this->render('metals/add_metals.html.twig',
                [
                    'errors' => $messages,
                    'user' => $currentUser,
                    'form' => $this->createForm(MetalsType::class)->createView()
                ]);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $this->metalsService->insert($metal);
            $this->addFlash('info', 'Create metal successfully!');
            return $this->redirectToRoute('all_metals');
        }
        return $this->render('metals/add_metals.html.twig',
            [
                'errors' => $messages,
                'user' => $currentUser,
                'form' => $this->createForm(MetalsType::class)->createView()
            ]);
    }

    /**
     * @Route("/dashboard/metals/edit/{id}", name="edit_metal", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param int $id
     * @return RedirectResponse|Response|null
     */
    public function editMetal(Request $request, int $id)
    {
        $messages = [];
        $currentUser = $this->userService->currentUser();
        $metal = $this->metalsService->getOneMetal($id);
        if ($currentUser->isUser() || null === $metal) {
            return $this->redirectToRoute('shop_index');
        }
        return $this->render('metals/edi_metals.html.twig',
            [
                'metal' => $metal,
                'errors' => $messages,
                'user' => $currentUser,
                'form' => $this->createForm(MetalsType::class)->createView()
            ]);
    }

    /**
     * @Route("/dashboard/metals/edit/{id}", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param int $id
     * @return RedirectResponse|Response|null
     */
    public function editMetalProcess(Request $request, int $id)
    {
        $messages = [];
        $currentUser = $this->userService->currentUser();
        $metal = $this->metalsService->getOneMetal($id);
        $form = $this->createForm(MetalsType::class, $metal);
        $form->handleRequest($request);
        $messages = $this->errorCollection($form, $messages);
        $data = $request->request->get('metals');
        if ($currentUser->isUser() || null === $metal) {
            return $this->redirectToRoute('shop_index');
        }
        if (!$currentUser->isAdmin() && !$currentUser->isAuthorMetal($metal)) {
            return $this->redirectToRoute('shop_index');
        }
        if ($metal->getTitle() !== $data['title'] && $this->metalsService->getTitle($data['title'])) {
            $messages[] = "This Title ( " . $data['title'] . " ) already token!";
            return $this->render('metals/edi_metals.html.twig',
                [
                    'metal' => $metal,
                    'errors' => $messages,
                    'user' => $currentUser,
                    'form' => $this->createForm(MetalsType::class)->createView()
                ]);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $this->metalsService->update($metal);
            $this->addFlash('info', 'Create metal successfully!');
            return $this->redirectToRoute('all_metals');
        }
        return $this->render('metals/edi_metals.html.twig',
            [
                'metal' => $metal,
                'errors' => $messages,
                'user' => $currentUser,
                'form' => $this->createForm(MetalsType::class)->createView()
            ]);
    }

    /**
     * @Route("/dashboard/metals/delete/{id}", name="delete_metal")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param int $id
     * @return Response|null
     */
    public function deleteMetals(Request $request, int $id)
    {
        $currentUser = $this->userService->currentUser();
        $metal = $this->metalsService->getOneMetal($id);
        $form = $this->createForm(MetalsType::class, $metal);
        $form->handleRequest($request);
        if ($currentUser->isUser() || null === $metal) {
            return $this->redirectToRoute('shop_index');
        }
        if (!$currentUser->isAdmin() && !$currentUser->isAuthorMetal($metal)) {
            return $this->redirectToRoute('shop_index');
        }
        $this->metalsService->delete($metal);
        $this->addFlash("info", "Delete metal successfully!");
        return $this->redirectToRoute('all_metals');
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
