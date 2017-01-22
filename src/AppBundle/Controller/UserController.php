<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/livreur")
 */
class UserController extends Controller
{
    /**
     * @Route("/chat", name="user_messagerie")
     */
    public function chatAction(Request $request)
    {
        $login = $this->getUser();
        $profil = $login->getProfil();
        $messages = $this->getDoctrine()->getRepository('AppBundle:Message')->findBy(['profil' => $profil], ['date' => 'ASC']);
        $params = [];
        $em = $this->getDoctrine()->getEntityManager();
        foreach ($messages as $message) {
            $livreur = $message->getProfil();
            $isAdmin = $message->getLogin()->getAdmin();
            if ($isAdmin && !$message->getVu()) {
                $message->setVu(true);
            }
            $params[] = [
                "from" => $isAdmin ? "admin" : $livreur->getPrenom() . " " . $livreur->getNom(),
                "photo" => $isAdmin ? "" : ($livreur->getPhoto() ? $this->getParameter('photo_path') . $livreur->getPhoto() : ""),
                "content" => $message->getMessage(),
                "date" => $message->getDate(),
            ];
        }
        $em->flush();

        // replace this example code with whatever you need
        return $this->render('user/chat.html.twig', [
            "messages" => $params
        ]);
    }


    /**
     * @Route("/chat/ajouter", name="user_chat_add")
     * @Method({"POST"})
     */
    public function chatAddAction(Request $request)
    {
        $messageText = $request->request->get('message');
        if ($messageText == null) {
            throw $this->createNotFoundException('User does not exist');
        }
        $login = $this->getUser();
        $profil = $login->getProfil();

        $message = new Message();
        $message->setDate(new \DateTime());
        $message->setProfil($profil);
        $message->setLogin($login);
        $message->setMessage($messageText);
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($message);
        $em->flush();

        return new Response("ok", 200);
    }

    /**
     * @Route("/", name="user_homepage")
     * @Route("/planning", name="user_planning")
     */
    public function planningAction(Request $request)
    {
        
        // replace this example code with whatever you need
        return $this->render('user/planning.html.twig', [
         ]);
    }



    /**
     * @Route("/factures", name="user_factures")
     */
    public function factureAction(Request $request)
    {
        $login = $this->getUser();
        $profil = $login->getProfil();
        $factures = $this->getDoctrine()->getRepository('AppBundle:Facture')->findBy(['profil' => $profil, 'etat' => true]);
        $params = [];
        foreach ($factures as $facture) {
            $params[] = [
                "id" => $facture->getId(),
                "date" => $facture->getDate(),
                "montant" => $facture->getMontant(),
                "etat" => $facture->getEtat(),
                "libelle" => $facture->getLibelle(),
                "profil" => $facture->getProfil()->getNom() . " " . $facture->getProfil()->getPrenom()
            ];
        }

        /*$params = [
            [
                "id" => "Min Ouche",
                "date" => new \DateTime(),
                "montant" => "290.45",
                "etat" => true,
                "libelle" => "Facture du mois de janvier 2016",
                "profil" => "Min Ouche"
            ],
            [
                "id" => "Min Ouche",
                "date" => new \DateTime(),
                "montant" => "290.45",
                "etat" => true,
                "libelle" => "Facture des trois dimanches de janvier",
                "profil" => "Min Ouche"
            ],
            [
                "id" => "Min Ouche",
                "date" => new \DateTime(),
                "montant" => "290.45",
                "etat" => false,
                "libelle" => "Facture septembre/octobre",
                "profil" => "Min Ouche"
            ],
            [
                "id" => "Min Ouche",
                "date" => new \DateTime(),
                "montant" => "290.45",
                "etat" => true,
                "libelle" => "Facture des trois dimanches de janvier",
                "profil" => "Min Ouche"
            ],
            [
                "id" => "Min Ouche",
                "date" => new \DateTime(),
                "montant" => "290.45",
                "etat" => false,
                "libelle" => "Facture septembre/octobre",
                "profil" => "Min Ouche"
            ],
            [
                "id" => "Min Ouche",
                "date" => new \DateTime(),
                "montant" => "290.45",
                "etat" => true,
                "libelle" => "Facture des trois dimanches de janvier",
                "profil" => "Min Ouche"
            ]
        ];*/

        
        // replace this example code with whatever you need
        return $this->render('user/factures.html.twig', [
            "factures" => $params
         ]);
    }




}
