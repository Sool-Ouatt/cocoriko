<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Commande;
use App\Entity\Contenu;
use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    /**
     * @Route("/panier", name="panier")
     */
    public function index(): Response
    {
        return $this->render('panier/index.html.twig', [
            'controller_name' => 'PanierController',
        ]);
    }

    /**
     * @Route("/panier/commandes/{id}", name="mesCommande")
     * @ParamConverter("clt", class="App\Entity\Client")
     */
    public function mesCommandes($clt):Response
    {
        return $this->render('panier/commandes.html.twig', [
            'client' => $clt->getTelephone(),
        ]);
    }

    /**
     * @Route("/panier/payer/{id}", name="payement")
     * @ParamConverter("cmd", class="App\Entity\Commande")
     */
    public function payements($cmd):Response
    {
        return $this->render('panier/payement.html.twig', [
            'commande' => $cmd->getId(),
        ]);
    }

    /**
     * @Route("/panier/valider", name="validation")
     */
    public function validationAchat(Request $request,EntityManagerInterface $em):Response
    {
        $session = $request->getSession();
        $valeurPanier = $session->get('valeurPanier');
        $nombreType = $session->get("nombreType");
        /*
         * pour Id commande nous prenons
         */
        $dateJour = date_format(new \DateTime(),'Y-m-d');
        $aleatoire = rand();
        $idCommande = $dateJour."-".((string)$aleatoire);

        $formAchat = $this->createFormBuilder()
            ->add('Prenom',TextType::class)
            ->add('Nom',TextType::class)
            ->add('Telephone',TelType::class)
            ->add('email',EmailType::class)
            ->add('date',DateType::class)
            ->add('Adresse',TextType::class)
            ->add('ModePayement', ChoiceType::class, [
                'choices'  => [
                    'En ligne' => 'Enligne',
                    'A la livraison' => 'Livraison',
                    'A la boutique' => 'Magasin', ],
                ])
            ->add('Valider', SubmitType::class)
            ->getForm();

        $formAchat->handleRequest($request);
        if($formAchat->isSubmitted() && $formAchat->isValid()){

            $donnees = $formAchat->getData();
            $em->getConnection()->beginTransaction();
            try {

                $modePayement = $donnees['ModePayement'];
                $telephone = $donnees['Telephone'];
                $prenom = $donnees['Prenom'];
                $nom = $donnees['Nom'];
                $email = $donnees['email'];
                $dateLivraison = $donnees['date'];
                $adresse = $donnees['Adresse'];

                $client = new Client();
                $commande = new Commande();


                $client->setTelephone($telephone);
                $client->setNom($nom);
                $client->setPrenom($prenom);
                $client->setEmail($email);

                $commande->setId($idCommande);
                $commande->setTeleploneClient($client);
                $commande->setDateCommande($dateLivraison);
                $commande->setAdresseLivraison($adresse);
                $commande->setValeur($valeurPanier);

                // verifie si le client existe deja
                $respository = $this->getDoctrine()->getRepository(Client::class);
                $clt = $respository->find($telephone);
                if ($clt == null){
                    $em->persist($client);
                    $em->persist($commande);
                 //parcourir les contenus et les persistés
                    for($i=0; $i <= $nombreType; $i++)
                    {
                        $produit = $session->get("produit".((string)($i+1)));
                        $respository = $this->getDoctrine()->getRepository(Produit::class);
                        $prod = $respository->find($produit[0]);
                        $prodQuantite = $produit[1];

                        $contenu = new Contenu();
                        $contenu->setIdCommande($commande);
                        $contenu->setIdProduit($prod);
                        $contenu->setQunatite($prodQuantite);
                        $em->persist($contenu);
                    }

                    $em->flush();
                    $em->getConnection()->commit();

                    if ($modePayement == 'Enligne')
                        return $this->redirectToRoute('payement',array('id'=>$idCommande));
                    else
                        return $this->redirectToRoute('mesCommande',array('id'=>$telephone));
                }else{
                    // gerer Id produit avec les sesssion................
                    return $this->redirectToRoute('mesCommande',array('id'=>$telephone));
                }

            }catch (Exception $e) {
                $em->getConnection()->rollBack();
                throw $e;
            }
        }
        return $this->render('panier/validation.html.twig', [
            'controller_name' => 'PanierController',
            'valeurPanier' => $valeurPanier,
            'alea' => $idCommande,
            'formulaire' => $formAchat->createView()
        ]);
    }

    /**
     * @Route ("/panier/ajout/{id}" , name="monPanier")
     * @ParamConverter("prod", class="App\Entity\Produit")
     */
    /*
     * Pour ajouter le produit au panier : on verifie si la session est active.
     * si oui on incremente la variableSession nombreType qui designe le nombre de produits differents dans le panier
     * si non nombreType est crée et mis a 1
     */
    public function gestionPanier($prod,Request $request,EntityManagerInterface $em): Response
    {
        /*
         * parcourir les contenus pour voir si le produit est déjà dedans,
         * si oui : dire que le produit existe deja dans le panier et incrementer le nombre
         * sinon ajouter le produit au panier
         */
        $session = $request->getSession();
        $verification = false;
        $leProduit = "";
        $quantiteAncien = 0;
        if($session->get("nombreType") !== null)
        {
            for($i=0; $i <= $session->get("nombreType"); $i++)
            {
                $produit = $session->get("produit".((string)($i+1)));
                if($prod->getId() == $produit[0])
                {
                    $verification = true;
                    $leProduit = "produit".((string)($i+1));
                    $quantiteAncien = $produit[3];
                    $this->addFlash("msProduitExiste","Ce produit existe déjà dans le panier, voulez vous modifier sa quantité!\n
                    l'ancienne quantité est : ".$quantiteAncien);
                }
            }
        }
        if($verification)
        {
            $formDetail = $this->createFormBuilder()
                ->add('quantite',NumberType::class,['empty_data' => $quantiteAncien])
                ->add('Suivant', SubmitType::class)
                ->getForm();
        }else{
            $formDetail = $this->createFormBuilder()
                ->add('quantite',NumberType::class)
                ->add('Suivant', SubmitType::class)
                ->getForm();
        }

        $formDetail->handleRequest($request);
        if($formDetail->isSubmitted() && $formDetail->isValid()){

            $donnees = $formDetail->getData();
            $em->getConnection()->beginTransaction();
            try {
                $laQuantite = $donnees['quantite'];
                $valeur = $prod->getPrixVente() * $laQuantite;
                if($session->get("nombreType") !== null)
                {
                    //si le produit existe
                    if($verification)
                    {
                        //ici on enlève l'ancienne valeur au panier et on ajoute la nouvelle valeur
                        $val = $session->get("valeurPanier");
                        $valAncienne = $prod->getPrixVente() * $quantiteAncien;
                        $val -= $valAncienne;
                        $val += $valeur;
                        $session->set($leProduit,[$prod->getId(),$prod->getDesignation(),$prod->getPrixVente(),$laQuantite]);
                        $session->set("valeurPanier",$val);
                    }else{
                        $nb = $session->get("nombreType");
                        $val = $session->get("valeurPanier");
                        $val += $valeur;
                        $nb += 1;
                        $session->set("nombreType", $nb);
                        $session->set("produit".((string)$nb),[$prod->getId(),$prod->getDesignation(),$prod->getPrixVente(),$laQuantite]);
                        $session->set("valeurPanier",$val);
                    }
                }else {
                    $session->set("nombreType", 1);
                    $session->set("produit1",[$prod->getId(),$prod->getDesignation(),$prod->getPrixVente(),$laQuantite]);
                    $session->set("valeurPanier",$valeur);
                }

                return $this->redirectToRoute('validation');

            }catch (Exception $e) {
                $em->getConnection()->rollBack();
                throw $e;
            }
        }
        $respository = $this->getDoctrine()->getRepository(Produit::class);
        $produits = $respository->lesProduits();
        return $this->render('panier/monPanier.html.twig', [
            'produit' => $prod,
            'lesProduits' => $produits,
            'formulaire' => $formDetail->createView()
        ]);
    }
}
