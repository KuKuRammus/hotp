<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Form\ProtectedMessageContent;
use App\Entity\QrCodePayload;
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
     * @Route("", methods={"GET"}, name="home.index")
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

            $qrCodePayload = QrCodePayload::createFromProtectedMessage($protectedMessage);
            $this->addFlash(QrCodePayload::class, json_encode($qrCodePayload));

            return $this->redirectToRoute('code.index');
        }

        return $this->render('content/home.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
