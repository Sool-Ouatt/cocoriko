<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Produit;
use App\Repository\ProduitRepository;
use App\Form\ProduitType;

class ProduitController extends AbstractController
{
    /**
     * @Route("bg_admin/compte/produits", name="bg_produits")
     */
    public function index(ProduitRepository $q, PaginatorInterface $paginator, Request $request): Response
    {
        $data = $q->findAll();
        $produits = $paginator->paginate($data, $request->query->getInt('page', 1), 10);

        return $this->render('produit/index.html.twig', [
            'produits' => $produits
        ]);
    }

    /**
     * @Route("bg_admin/compte/nouveau_produit", name="bg_create_produit")
     * @Route("bg_admin/compte/{id}/modification_produit", name="bg_edit_produit")
     */
    public function save($id = null, ProduitRepository $q, Request $request) {

        if(!$id) {
            $produit = new Produit();
            $old = null;
        } else {
            $produit = $q->find($id);
            $old = $produit->getImage();
        }

        $produitForm = $this->createForm(ProduitType::class, $produit);
        $produitForm->handleRequest($request);

        $manager = $this->getDoctrine()->getManager();
        if ($produitForm->isSubmitted() && $produitForm->isValid()) {

        // Update Date
            $produit->setCreatedAt(new \DateTime());
            // Upload File
            if (null !==  $produitForm['image']->getData()) {
        // clear old image
                if(null !== $old) {
                    $oldFile = $this->getParameter('produit_directory').'/'.$old;
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }
                $file = $produitForm['image']->getData();
                $filename = md5(uniqid()).'.'.$file->guessExtension();
                $produit->setImage($filename);
                $file->move($this->getParameter('produit_directory'), $filename);
            } else {
                $filename = $old;
                $produit->setImage($filename);
            }
            // data persister
            $manager->persist($produit);
            $manager->flush();

            // redirect to produits page
            return $this->redirectToRoute('bg_produits');
        }

        return $this->render('produit/single_produit.html.twig', [
            'produitForm' => $produitForm->createView(),
            'editMode' => $produit->getId() !== null,
        ]);

    }

    /**
     * @Route("bg_admin/compte/{id}/delete_produit", name="bg_delete_produit")
     */
    public function delete($id, ProduitRepository $q): Response
    {
        $produit = $q->find($id);
            // cleared old product image
        $oldFile = $this->getParameter('produit_directory').'/'.$produit->getImage();
        if (file_exists($oldFile)) {
            unlink($oldFile);
        }
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($produit);
        $manager->flush();

        return $this->redirectToRoute('bg_produits');
    }
}
