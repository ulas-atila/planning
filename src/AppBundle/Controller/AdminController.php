<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * @Route("/chat", name="admin_chat")
     */
    public function chatAction(Request $request)
    {
        $params = [
            [
                "from" => "Toto",
                "photo" => "http://allenbukoff.com/newwavepsychology/JohnDoeMasthead.jpg",
                "content" => "Mon Message",
                "date" => new \DateTime()
            ],
            [
                "from" => "admin",
                "photo" => "http://socialpro.miguelvasquez.net/public/avatar/large_johndoe_18gu2qv.jpg",
                "content" => "Mon Message",
                "date" => new \DateTime()
            ],
            [
                "from" => "toto",
                "photo" => "http://allenbukoff.com/newwavepsychology/JohnDoeMasthead.jpg",
                "content" => "Mon Message",
                "date" => new \DateTime()
            ]
        ];

        // replace this example code with whatever you need
        return $this->render('admin/chat.html.twig', [
            "messages" => $params
        ]);
    }


    /**
     * @Route("/list", name="admin_list")
     */
    public function listAction(Request $request)
    {
        $params = [
            [
                "id" => "1",
                "photo" => "http://allenbukoff.com/newwavepsychology/JohnDoeMasthead.jpg",
                "prenom" => "John",
                "nom" => "Doe",
                "ville" => "Milly-La-Forêt"
            ],
            [
                "id" => "2",
                "photo" => "http://allenbukoff.com/newwavepsychology/JohnDoeMasthead.jpg",
                "prenom" => "Michel",
                "nom" => "Dupont",
                "ville" => "Drancy"
            ],
            [
                "id" => "3",
                "photo" => "http://allenbukoff.com/newwavepsychology/JohnDoeMasthead.jpg",
                "prenom" => "Min",
                "nom" => "Ouche",
                "ville" => "Chatou"
            ]
        ];

        // replace this example code with whatever you need
        return $this->render('admin/liste.html.twig', [
            "livreurs" => $params
        ]);
    }

    /**
     * @Route("/add_livreur", name="add_livreur")
     */
    public function add_livreurAction(Request $request)
    {
        
        // replace this example code with whatever you need
        return $this->render('admin/add_livreur.html.twig', [
         ]);
    }

    /**
     * @Route("/modifier_livreur", name="modifier_livreur")
     */
    public function modifier_livreurAction(Request $request)
    {
        
        // replace this example code with whatever you need
        return $this->render('admin/modifier_livreur.html.twig', [
         ]);
    }

    /**
     * @Route("/messagerie", name="messagerie")
     */
    public function messagerieAction(Request $request)
    {
        $params = [
            [
                "from" => "Michel Dupont",
                "photo" => "http://allenbukoff.com/newwavepsychology/JohnDoeMasthead.jpg",
                "content" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.",
                "date" => new \DateTime(),
                "vu" => true
            ],
            [
                "from" => "Jean-Michel Ulas",
                "photo" => "http://socialpro.miguelvasquez.net/public/avatar/large_johndoe_18gu2qv.jpg",
                "content" => "Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.",
                "date" => new \DateTime(),
                "vu" => true
            ],
            [
                "from" => "John Doe",
                "photo" => "http://allenbukoff.com/newwavepsychology/JohnDoeMasthead.jpg",
                "content" => "Entre toi et moi il y a un produit qui s'appelle un produit, et c'est un produit qui s'appelle l'oxygène, alors si tu fais ça (inspiration/expiration) comme ça, tu vis, mais si je tue l'oxygène comme sur la lune, tu meurs !",
                "date" => new \DateTime(),
                "vu" => false
            ]
        ];

        
        // replace this example code with whatever you need
        return $this->render('admin/messagerie.html.twig', [
            "messages" => $params
         ]);
    }

    /**
     * @Route("/factures", name="factures")
     */
    public function factureAction(Request $request)
    {


        $params = [
            [
                "id" => "Michel Dupont",
                "date" => new \DateTime(),
                "montant" => "290.45",
                "etat" => true,
                "libelle" => "Facture du mois de janvier 2016",
                "profil" => "Michel Dupont"
            ],
            [
                "id" => "Michel Dupont",
                "date" => new \DateTime(),
                "montant" => "290.45",
                "etat" => true,
                "libelle" => "Facture des trois dimanches de janvier",
                "profil" => "Michel Dupont"
            ],
            [
                "id" => "Michel Dupont",
                "date" => new \DateTime(),
                "montant" => "290.45",
                "etat" => true,
                "libelle" => "Facture septembre/octobre",
                "profil" => "Michel Dupont"
            ]
        ];

        
        // replace this example code with whatever you need
        return $this->render('admin/liste_factures.html.twig', [
            "factures" => $params
         ]);
    }
}