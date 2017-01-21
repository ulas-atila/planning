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
     * @Route("/chat", name="chat_admin")
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
     * @Route("/list", name="list_admin")
     */
    public function listAction(Request $request)
    {
        $params = [
            [
                "id" => "1",
                "photo" => "http://allenbukoff.com/newwavepsychology/JohnDoeMasthead.jpg",
                "prenom" => "John",
                "nom" => "Doe"
            ],
            [
                "id" => "2",
                "photo" => "http://allenbukoff.com/newwavepsychology/JohnDoeMasthead.jpg",
                "prenom" => "Michel",
                "nom" => "Dupont"
            ],
            [
                "id" => "3",
                "photo" => "http://allenbukoff.com/newwavepsychology/JohnDoeMasthead.jpg",
                "prenom" => "Min",
                "nom" => "Ouche"
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
}
