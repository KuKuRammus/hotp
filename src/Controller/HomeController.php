<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Form\ProtectedMessageContent;
use App\Form\CodeMessageType;
use App\Service\ProtectedMessageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController
 * @package App\Controller
 *
 * @Route("/")
 */
final class HomeController extends AbstractController
{

    private ProtectedMessageService $protectedMessageService;

    public function __construct(ProtectedMessageService $protectedMessageService)
    {
        $this->protectedMessageService = $protectedMessageService;
    }

    /**
     * @Route("", methods={"GET"})
     */
    public function index() {

        $codeMessage = new ProtectedMessageContent();
        $form = $this->createForm(CodeMessageType::class, $codeMessage);

        return $this->render('content/home.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("", methods={"POST"})
     */
    public function createNewCode(Request $request) {
        $messageContent = new ProtectedMessageContent();
        $form = $this->createForm(CodeMessageType::class, $messageContent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ProtectedMessageContent $codeMessage */
            $messageContent = $form->getData();

            $protectedMessage = $this->protectedMessageService->create($messageContent);

            return new Response("content received: ".$protectedMessage->getSecret());
        }

        return $this->render('content/home.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
