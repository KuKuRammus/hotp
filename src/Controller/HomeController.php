<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        return $this->render('content/home.html.twig');
    }
}
