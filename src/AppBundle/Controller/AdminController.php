<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Profil;
use AppBundle\Entity\Login;
use AppBundle\Entity\Message;
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
     * @Route("/chat/{userId}", name="admin_chat")
     */
    public function chatAction(Request $request, $userId)
    {
        $messages = $this->getDoctrine()->getRepository('AppBundle:Message')->findByUserId($userId);
        $params = [];
        $em = $this->getDoctrine()->getEntityManager();
        foreach ($messages as $message) {
            $livreur = $message->getProfil();
            $isAdmin = $message->getLogin()->getAdmin();
            if (!$isAdmin && !$message->getVu()) {
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
        return $this->render('admin/chat.html.twig', [
            "messages" => $params,
            "user_id" => $userId
        ]);
    }

    /**
     * @Route("/chat/{userId}/ajouter", name="admin_chat_add")
     * @Method({"POST"})
     */
    public function chatAddAction(Request $request, $userId)
    {
        $messageText = $request->request->get('message');
        if ($messageText == null) {
            throw $this->createNotFoundException('User does not exist');
        }
        $profil = $this->getDoctrine()->getRepository('AppBundle:Profil')->find($userId);
        if ($profil == null) {
            throw $this->createNotFoundException('User does not exist');
        }
        $message = new Message();
        $message->setDate(new \DateTime());
        $message->setProfil($profil);
        $message->setLogin($this->getUser());
        $message->setMessage($messageText);
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($message);
        $em->flush();

        return new Response("ok", 200);
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
     * @Route("/livreur/ajouter", name="add_livreur", defaults={"userId": null})
     * @Route("/livreur/modifier/{userId}", name="edit_livreur")
     */
    public function addLivreurAction(Request $request, $userId)
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
     * @Route("/messagerie", name="messagerie")
     */
    public function messagerieAction(Request $request)
    {
        $messages = $this->getDoctrine()->getRepository('AppBundle:Message')->findLasts();
        $params = [];
        foreach ($messages as $message) {
            $livreur = $message->getProfil();
            $params[] = [
                "livreur_id" => $livreur->getId(),
                "from" => $livreur->getPrenom() . " " . $livreur->getNom(),
                "photo" => $livreur->getPhoto() ? $this->getParameter('photo_path') . $livreur->getPhoto() : "",
                "content" => $message->getMessage(),
                "date" => $message->getDate(),
                "vu" => $message->getLogin()->getAdmin() || $message->getVu()
            ];
        }
        
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