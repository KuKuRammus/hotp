<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Form\TimeFrameCode;
use App\Entity\GeneratorTimeProvider;
use App\Form\TimeFrameCodeType;
use App\Service\CodeGeneratorService;
use App\Service\ProtectedMessageService;
use DateTimeImmutable;
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
    private CodeGeneratorService $codeGeneratorService;

    public function __construct(
        ProtectedMessageService     $protectedMessageService,
        CodeGeneratorService        $codeGeneratorService
    )
    {
        $this->protectedMessageService = $protectedMessageService;
        $this->codeGeneratorService = $codeGeneratorService;
    }

    /**
     * @Route("", methods={"GET", "POST"}, name="check.index")
     */
    public function index(Request $request, string $name) {
        $protectedMessage = $this->protectedMessageService->getOneByNameOrNull($name);
        if ($protectedMessage === null) {
            return $this->redirectToRoute('home.index');
        }

        $timeFrameCode = new TimeFrameCode();
        $form = $this->createForm(TimeFrameCodeType::class, $timeFrameCode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TimeFrameCode $timeFrameCode */
            $timeFrameCode = $form->getData();

            $currentTimeFrameCode = $this->codeGeneratorService->generateUsingSecret(
                $protectedMessage->getSecret(),
                new GeneratorTimeProvider((new DateTimeImmutable())->getTimestamp())
            );

            $isCodeMatches = $timeFrameCode->getCode() === $currentTimeFrameCode;

            return $this->render('content/check.html.twig', [
                'protectedMessage' => $protectedMessage,
                'isFormSubmitted' => true,
                'isCodeMatches' => $isCodeMatches,
                'protectedMessageContent' => $isCodeMatches ? $protectedMessage->getContent()->getContent() : '',
                'form' => $form->createView()
            ]);
        }

        return $this->render('content/check.html.twig', [
            'protectedMessage' => $protectedMessage,
            'isFormSubmitted' => ($form->isSubmitted() && $form->isValid()),
            'isCodeMatches' => false,
            'protectedMessageContent' => '',
            'form' => $form->createView()
        ]);
    }
}
