<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/livreur")
 */
class UserController extends Controller
{
    /**
     * @Route("/", name="user_homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }


    /**
     * @Route("/chat", name="user_messagerie")
     */
    public function chatAction(Request $request)
    {
        $login = $this->getDoctrine()->getRepository('AppBundle:Login')->findOneByEmail('admin@admin.fr');
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
        return $this->render('user/chat.html.twig', [
            "messages" => $params
        ]);
    }




    /**
     * @Route("/planning", name="user_planning")
     */
    public function planningAction(Request $request)
    {
        
        // replace this example code with whatever you need
        return $this->render('user/planning.html.twig', [
         ]);
    }



    /**
     * @Route("/factures", name="factures")
     */
    public function factureAction(Request $request)
    {


        $params = [
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
        ];

        
        // replace this example code with whatever you need
        return $this->render('user/factures.html.twig', [
            "factures" => $params
         ]);
    }




}
