<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Profil;
use AppBundle\Entity\Login;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type as FormType;

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
        return $this->render('admin/chat.html.twig', [
            "messages" => $params
        ]);
    }


    /**
     * @Route("/livreurs", name="admin_list")
     */
    public function listAction(Request $request)
    {
        $livreurs = $this->getDoctrine()->getRepository('AppBundle:Profil')->findByActive(true);
        $params = [];
        foreach ($livreurs as $livreur) {
            $params[] = [
                "id" => $livreur->getId(),
                "photo" => $livreur->getPhoto() ? $this->getParameter('photo_path') . $livreur->getPhoto() : "",
                "prenom" => $livreur->getPrenom(),
                "nom" => $livreur->getNom(),
                "ville" => $livreur->getVilledelivraison()
            ];
        }

        // replace this example code with whatever you need
        return $this->render('admin/liste.html.twig', [
            "livreurs" => $params
        ]);
    }

    /**
     * @Route("/livreur/ajouter", name="add_livreur")
     * @Route("/livreur/modifier/{userId}", name="edit_livreur")
     */
    public function addLivreurAction(Request $request, $userId = null)
    {
        $profil;
        if ($userId === null) {
            $profil = new Profil();
        } else {
            $profil = $this->getDoctrine()
                ->getRepository('AppBundle:Profil')
                ->find($userId);
            if ($profil === null) {
                $profil = new Profil();
            }
        }
        $previousPhoto = $profil->getPhoto();
        $previousEmail = $profil->getEmail();
        $profil->setDateentree(new \DateTime());
        $form = $this->createProfilForm($profil, $userId === null);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $profil->getPhoto();
            if ($file === null) {
                $profil->setPhoto($previousPhoto);
            } else {
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $file->move(
                    $this->getParameter('photo_directory'),
                    $fileName
                );
                $profil->setPhoto($fileName);
            }

            $em = $this->getDoctrine()->getManager();
            
            if ($userId === null) {
                $login = new Login();
                $login->setEmail($profil->getEmail());
                $login->setPassword("password");
                $login->setProfil($profil);
                $em->persist($login);
            } else {
                $profil->setEmail($previousEmail);
            }

            $em->persist($profil);
            $em->flush();

            return $this->redirectToRoute('admin_list');
        }
        
        // replace this example code with whatever you need
        return $this->render('admin/add_livreur.html.twig', [
            'form' => $form->createView()
        ]);
    }

    private function createProfilForm(Profil $profil, $isNew = true)
    {
        $form = $this->createFormBuilder($profil)
            ->add('nom', FormType\TextType::class, [
                'attr' => ['placeholder' => 'Nom du livreur'],
                'label' => 'Nom'
            ])
            ->add('prenom', FormType\TextType::class, [
                'attr' => ['placeholder' => 'Prénom du livreur'],
                'label' => 'Prénom'
            ])
            ->add('datedenaissance', FormType\DateType::class, [
                'attr' => ['placeholder' => 'Date de naissance'],
                'label' => 'Date de naissance'
            ])
            ->add('email', FormType\EmailType::class, [
                'attr' => ['placeholder' => 'Email du livreur', 'readonly' => !$isNew],
                'label' => 'E-mail'
            ])
            ->add('adresse', FormType\TextType::class, [
                'attr' => ['placeholder' => 'Adresse postale'],
                'label' => 'Adresse postale du livreur'
            ])
            ->add('telephone', FormType\TextType::class, [
                'attr' => ['placeholder' => 'Téléphone'],
                'label' => 'Téléphone du livreur'
            ])
            ->add('villedelivraison', FormType\TextType::class, [
                'attr' => ['placeholder' => 'Ville de livraison'],
                'label' => 'Ville de livraion'
            ])
            ->add('siret', FormType\TextType::class, [
                'attr' => ['placeholder' => 'SIRET'],
                'label' => 'SIRET'
            ])
            ->add('rib', FormType\TextType::class, [
                'attr' => ['placeholder' => 'RIB'],
                'label' => 'RIB'
            ])
            ->add('photo', FormType\FileType::class, [
                'attr' => ['placeholder' => 'photo'],
                'label' => 'Photo',
                'data_class' => null
            ])
            ->getForm();
        ;

        return $form;
    }

    /**
     * @Route("/livreur/supprimer", name="delete_livreur")
     * @Method({"POST"})
     */
    public function deleteLivreurAction(Request $request)
    {
        $user_id = $request->request->get('user_id');

        if ($user_id == null) {
            throw $this->createNotFoundException('User does not exist');
        }

        $profil = $this->getDoctrine()->getRepository('AppBundle:Profil')->find($user_id);
        if ($profil == null) {
            throw $this->createNotFoundException('User does not exist');
        }

        $profil->setActive(false);

        $em = $this->getDoctrine()->getEntityManager()->flush();

        return new Response("ok", 200);
    }


    /**
     * @Route("/planning", name="admin_planning")
     */
    public function planningAction(Request $request)
    {
        
        // replace this example code with whatever you need
        return $this->render('admin/planning.html.twig', [
         ]);
    }

    /**
     * @Route("/facture/ajouter", name="admin_add_facture")
     */
    public function addfactureAction(Request $request)
    {
        $params = [
            [
                "nom" => "Ouche",
                "prenom" => "Min",
                "id" => 1
            ],
            [
                "nom" => "Edge",
                "prenom" => "Lee",
                "id" => 4
            ],
            [
                "nom" => "Nano",
                "prenom" => "Teknologik93",
                "id" => 2
            ]
            
        ];
        
        // replace this example code with whatever you need
        return $this->render('admin/add_facture.html.twig', [
            "livreurs" => $params]);
    }


    /**
     * @Route("/facture/modifier", name="admin_edit_facture")
     */
    public function editfactureAction(Request $request)
    {
        $params = [
            [
                "nom" => "Couchette",
                "prenom" => "Lee",
                "id" => 1
            ],
            
        ];
        
        // replace this example code with whatever you need
        return $this->render('admin/edit_facture.html.twig', [
            "livreurs" => $params]);
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
                "profil" => "John Doe"
            ],
            [
                "id" => "Michel Dupont",
                "date" => new \DateTime(),
                "montant" => "290.45",
                "etat" => true,
                "libelle" => "Facture des trois dimanches de janvier",
                "profil" => "Min Ouche"
            ],
            [
                "id" => "Michel Dupont",
                "date" => new \DateTime(),
                "montant" => "290.45",
                "etat" => false,
                "libelle" => "Facture septembre/octobre",
                "profil" => "Michel Dupont"
            ],
            [
                "id" => "Michel Dupont",
                "date" => new \DateTime(),
                "montant" => "290.45",
                "etat" => true,
                "libelle" => "Facture des trois dimanches de janvier",
                "profil" => "Min Ouche"
            ],
            [
                "id" => "Michel Dupont",
                "date" => new \DateTime(),
                "montant" => "290.45",
                "etat" => false,
                "libelle" => "Facture septembre/octobre",
                "profil" => "Michel Dupont"
            ],
            [
                "id" => "Michel Dupont",
                "date" => new \DateTime(),
                "montant" => "290.45",
                "etat" => true,
                "libelle" => "Facture des trois dimanches de janvier",
                "profil" => "Min Ouche"
            ],
            [
                "id" => "Michel Dupont",
                "date" => new \DateTime(),
                "montant" => "290.45",
                "etat" => false,
                "libelle" => "Facture septembre/octobre",
                "profil" => "Michel Dupont"
            ],
            [
                "id" => "Michel Dupont",
                "date" => new \DateTime(),
                "montant" => "290.45",
                "etat" => true,
                "libelle" => "Facture des trois dimanches de janvier",
                "profil" => "Min Ouche"
            ],
            [
                "id" => "Michel Dupont",
                "date" => new \DateTime(),
                "montant" => "290.45",
                "etat" => false,
                "libelle" => "Facture septembre/octobre",
                "profil" => "Michel Dupont"
            ],
            [
                "id" => "Michel Dupont",
                "date" => new \DateTime(),
                "montant" => "290.45",
                "etat" => true,
                "libelle" => "Facture des trois dimanches de janvier",
                "profil" => "Min Ouche"
            ],
            [
                "id" => "Michel Dupont",
                "date" => new \DateTime(),
                "montant" => "290.45",
                "etat" => false,
                "libelle" => "Facture septembre/octobre",
                "profil" => "Michel Dupont"
            ],
            [
                "id" => "Michel Dupont",
                "date" => new \DateTime(),
                "montant" => "290.45",
                "etat" => true,
                "libelle" => "Facture des trois dimanches de janvier",
                "profil" => "Min Ouche"
            ],
            [
                "id" => "Michel Dupont",
                "date" => new \DateTime(),
                "montant" => "290.45",
                "etat" => false,
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