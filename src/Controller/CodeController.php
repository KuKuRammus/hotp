<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\QrCodePayload;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CodeController
 * @package App\Controller
 *
 * @Route("/code")
 */
final class CodeController extends AbstractController
{
    /**
     * @Route("", methods={"GET"}, name="code.index")
     */
    public function index(Request $request) {
        $input = $this->get('session')->getFlashBag()->get(QrCodePayload::class, []);
        if (count($input) === 0) {
            return $this->redirectToRoute('home.index');
        }

        $qrCodePayload = QrCodePayload::createFromJson($input[0]);
        $qrOptions = new QROptions(
            [
                'outputType' => QRCode::OUTPUT_MARKUP_SVG,
                'quietzoneSize' => 0
            ]
        );
        $qrCodeImage = (new QRCode($qrOptions))->render(json_encode($qrCodePayload));

        return $this->render('content/code.html.twig', [
            'qrPayload' => $qrCodePayload,
            'qrImage' => $qrCodeImage
        ]);
    }
}
