<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\ProtectedMessageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CheckController
 * @package App\Controller
 *
 * @Route("/check/{name}")
 */
final class CheckController extends AbstractController
{
    private ProtectedMessageService $protectedMessageService;

    public function __construct(ProtectedMessageService $protectedMessageService)
    {
        $this->protectedMessageService = $protectedMessageService;
    }

    /**
     * @Route("", methods={"GET"}, name="check.index")
     */
    public function index(Request $request, string $name) {
        $protectedMessage = $this->protectedMessageService->getOneByNameOrNull($name);
        if ($protectedMessage === null) {
            return $this->redirectToRoute('home.index');
        }

        return $this->render('content/check.html.twig', [
            'protectedMessage' => $protectedMessage
        ]);
    }
}
