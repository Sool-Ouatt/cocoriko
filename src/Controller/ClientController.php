<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Entreprise;
use App\Entity\User;
use App\Form\EntrepriseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
    /**
     * @Route("/client", name="client")
     */
    public function index(): Response
    {
        return $this->render('client/index.html.twig', [
            'controller_name' => 'ClientController',
        ]);
    }

    /**
     * @Route("/client/inscription", name="Enregistrer")
     */
    public function register(Request $request,EntityManagerInterface $em,UserPasswordHasherInterface $passwordHasher,\Swift_Mailer $mailer): Response
    {
        $formInscription = $this->createForm(EntrepriseType::class);

        $formInscription->handleRequest($request);
        if($formInscription->isSubmitted() && $formInscription->isValid()){

            $donnees = $formInscription->getData();
            $em->getConnection()->beginTransaction();
            try {
                $newUser = new User();
                $newClient = new Client();
                //$newEntreprise = new Entreprise();

                $newClient->setTelephone($formInscription->get('telephoneEntreprise')->getData());
                $newClient->setPrenom($formInscription->get('prenom')->getData());
                $newClient->setNom($formInscription->get('nom')->getData());
                $newClient->setEmail($formInscription->get('email')->getData());

                $newUser->setUsername($formInscription->get('username')->getData());
                $newUser->setEmail($formInscription->get('email')->getData());
                $newUser->setPassword($passwordHasher->hashPassword($newUser,$formInscription->get('motDePasse')->getData()));
                //ici on met le role à ROLE_DESACTIF, lorsqu'il valide après on met le role à ROLE_CLIENT_ENTREPRISE
                $newUser->setRoles(array("ROLE_DESACTIF"));
                $newUser->setTextValidation($this->generationValidate());
                $newUser->setTelephoneClient($newClient);

                $em->persist($newClient);
                $em->persist($newUser);
                $donnees->setTelephoneEntreprise($newClient);
                $em->persist($donnees);
                $em->flush();

                //Pour l'email de confirmation
                $message = (new \Swift_Message('Validation compte'))
                    ->setFrom('soolaymane.coulibaly@gmail.com')
                    ->setTo($newUser->getEmail())
                    ->setBody( $this->renderView(
                    //templates/emails/validation.html.twig
                        'emails/validation.html.twig', [
                        'name'  => $newUser->getUsername(),
                        'email' => $newUser->getEmail(),
                        'textValidation' => $newUser->getTextValidation()
                    ]),
                        'text/html')
                ;
                $mailer->send($message);

                $em->getConnection()->commit();

                $this->addFlash("msSucces","Votre compte a été crée avec succès!!!\n
                Et un email de confirmation vous a été envoyer, veuiller le validé s'il vous plait avant de vous connecter!");

                return $this->redirectToRoute('app_login');
            }catch (Exception $e) {
                $em->getConnection()->rollBack();
                throw $e;
            }
        }

        return $this->render('client/register.html.twig', [
            'controller_name' => 'ClientController',
            'formulaireInscript' => $formInscription->createView(),
        ]);
    }

    /**
     * @Route("/validation/{email}/{numValidation}", name="validate")
     */
    public function validationCompte(string $email,string $numValidation): Response
    {
        $respository = $this->getDoctrine()->getRepository(User::class);
        $user = $respository->findOneBy(["email" => $email,"textValidation" => $numValidation]);
        if($user)
        {
            $user->setTextValidation("");
            $user->setRoles(array("ROLE_CLIENT_ENTREPRISE"));
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash("msSucces"," Votre compte a été valider, vous pouvez vous connecter maintenant");
            return $this->redirectToRoute("app_login");
        }else {
            $this->addFlash("msError"," Désolé, ce lien n'est pas valide!!!");
            return $this->redirectToRoute("Register");
        }
    }

    /*
     * génération du string de validation
    */
    /**
     * @return string
     * @throws \Exception
     */
    public function generationValidate()
    {
        $dateJour = date_format(new \DateTime(),'Y-m-d');
        $bytes = random_bytes(5);
        return  str_replace("-","",$dateJour).bin2hex($bytes);
        // return rtrim(strtr(base64_decode(random_bytes(32)),'+/','-_'),'=');
    }
}
