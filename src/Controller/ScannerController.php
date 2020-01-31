<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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
     * @Route("", methods={"GET"})
     */
    public function index() {
        return new Response("scanner");
    }
}
