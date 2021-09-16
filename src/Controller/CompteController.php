<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompteController extends AbstractController
{
    /**
     * @Route("/bg_admin/compte", name="bg_compte")
     */
    public function index(): Response
    {
        return $this->render('compte/index.html.twig');
    }
}
