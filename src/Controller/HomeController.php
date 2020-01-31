<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Form\CodeMessage;
use App\Form\CodeMessageType;
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
    /**
     * @Route("", methods={"GET"})
     */
    public function index() {

        $codeMessage = new CodeMessage();
        $form = $this->createForm(CodeMessageType::class, $codeMessage);

        return $this->render('content/home.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @param Request $request
     *
     * @Route("", methods={"POST"})
     */
    public function createNewCode(Request $request) {
        $codeMessage = new CodeMessage();
        $form = $this->createForm(CodeMessageType::class, $codeMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CodeMessage $codeMessage */
            $codeMessage = $form->getData();

            return new Response("content received: ".$codeMessage->getMessage());
        }

        return $this->render('content/home.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
