<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Commande;
use App\Repository\CommandeRepository;

class CommandeController extends AbstractController
{
    /**
     * @Route("/bg_admin/compte/commandes", name="bg_commandes")
     */
    public function index(CommandeRepository $q, PaginatorInterface $paginator, Request $request): Response
    {
        $data = $q->findAll();
        $commandes = $paginator->paginate($data, $request->query->getInt('page', 1), 10);

        return $this->render('commande/index.html.twig', [
            'commandes' => $commandes
        ]);
    }
    /**
     * @Route("bg_admin/compte/commandes/{id}", name="bg_details")
     */
    public function single_commande($id = null, CommandeRepository $q, Request $request) {

        $commande = $q->find($id);

        return $this->render('commande/single_commande.html.twig', [
            'commande' => $commande
        ]);

    }
}
