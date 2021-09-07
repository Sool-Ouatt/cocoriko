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
    //si l'utilisateur est connecter et que le roles est "ROLE_DESACTIF", on le deconnecte immediatement et affiche un message
    public function index(): Response
    {
        if($this->getUser() && $this->getUser()->getRoles()[0] == "ROLE_DESACTIF")
        {
            $this->addFlash("msValide","Ce compte est innactif, veuillez verifier votre boit email pour l'activer !!!");
            return $this->redirectToRoute("app_logout");
        }else
        {
            $respository = $this->getDoctrine()->getRepository(Produit::class);
            $produits = $respository->lesProduits();
            return $this->render('acceuil/index.html.twig', [
                'controller_name' => 'AcceuilController',
                'lesProduits' => $produits
            ]);
        }
    }
}
