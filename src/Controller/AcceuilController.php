<?php

namespace App\Controller;

use App\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AcceuilController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $respository = $this->getDoctrine()->getRepository(Produit::class);
        $produits = $respository->lesProduits();
        return $this->render('acceuil/index.html.twig', [
            'controller_name' => 'AcceuilController',
            'lesProduits' => $produits
        ]);
    }
}
