<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CheckController
 * @package App\Controller
 *
 * @Route("/check")
 */
final class CheckController extends AbstractController
{
    /**
     * @Route("", methods={"GET"})
     */
    public function index() {
        return new Response("check");
    }
}
