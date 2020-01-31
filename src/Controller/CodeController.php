<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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
     * @Route("", methods={"GET"})
     */
    public function index() {
        return new Response("code");
    }
}
