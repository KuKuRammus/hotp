<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ScannerController
 * @package App\Controller
 *
 * @Route("/scanner")
 */
final class ScannerController extends AbstractController
{
    /**
     * @Route("", methods={"GET"}, name="scanner.index")
     */
    public function index() {
        return $this->render('content/scanner.html.twig');
    }
}
