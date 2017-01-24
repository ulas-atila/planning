<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Profil;
use AppBundle\Entity\Login;
use AppBundle\Entity\Message;
use AppBundle\Entity\Facture;
use AppBundle\Entity\Attribuee;
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
            'form' => $form->createView(),
            'isNew' => $userId === null
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
     * @Route("/", name="admin_homepage")
     * @Route("/planning", name="admin_planning")
     */
    public function planningAction(Request $request)
    {
        $date = new \DateTime($request->request->get('jour'));
        $day = date('w', $date->getTimestamp()) - 1;
        if ($day == -1) $day = 6;

        $dateDebut = new \DateTime();
        $dateDebut->setTimestamp($date->getTimestamp());
        $dateDebut->setTime(0,0,0);
        $dateDebut->modify('-' . $day . ' days');

        $dateFin = new \DateTime();
        $dateFin->setTimestamp($date->getTimestamp());
        $dateFin->setTime(0,0,0);
        $dateFin->modify('+' . (7-$day) . ' days');

        $disponibilites = $this->getDoctrine()->getRepository('AppBundle:Disponibilite')->findForAdmin($dateDebut, $dateFin);
        $attribuees = $this->getDoctrine()->getRepository('AppBundle:Attribuee')->findForAdmin($dateDebut, $dateFin);
        $paramsDisponibilites = $this->convertDisponibilitesToParams($disponibilites, $dateDebut);
        $paramsAttribuees = $this->convertDisponibilitesToParams($attribuees, $dateDebut);

        if ($request->request->get('day') !== null) {
            $creneau = $request->request->get('creneau');
            $day = $request->request->get('day');

            list($creneau_debut, $creneau_fin) = explode(' - ', $creneau);

            $creneau_debut = explode('H', $creneau_debut);
            $creneau_fin = explode('H', $creneau_fin);

            $dateDebutSemaine = $dateDebut;
            $dateDebut = new \DateTime();
            $dateDebut->setTimestamp($dateDebutSemaine->getTimestamp());
            $dateDebut->modify('+' . $day . ' days');
            $dateFin = new \DateTime();
            $dateFin->setTimestamp($dateDebut->getTimestamp());

            $dateDebut->setTime($creneau_debut[0], $creneau_debut[1]);
            $dateFin->setTime($creneau_fin[0], $creneau_fin[1]);

            $em = $this->getDoctrine()->getEntityManager();
            $attribuees = $this->getDoctrine()->getRepository('AppBundle:Attribuee')->findForAdmin($dateDebut, $dateFin);
            $profils = $this->getDoctrine()->getRepository('AppBundle:Profil')->getById($request->request->get('users', []));
            foreach ($attribuees as $attribuee) {
                $profilId = $attribuee->getProfil()->getId();
                if (!array_key_exists($profilId, $profils)) {
                    $em->remove($attribuee);
                    unset($paramsAttribuees[$creneau][$day][$profilId]);
                }
            }
            foreach ($profils as $profil) {
                if (!array_key_exists($profil->getId(), $attribuees)) {
                    $attribuee = new Attribuee();
                    $attribuee->setDateDebut($dateDebut);
                    $attribuee->setDateFin($dateFin);
                    $attribuee->setProfil($profil);
                    $em->persist($attribuee);
                    $paramsAttribuees[$creneau][$day][$profil->getId()] = ['prenom' => $profil->getPrenom(), 'nom' => $profil->getNom(), 'id' => $profil->getId()];
                }
            }
            $em->flush();
        }

        // replace this example code with whatever you need
        return $this->render('admin/planning.html.twig', [
            "date" => $date,
            'disponibilites' => $paramsDisponibilites,
            'attribuees' => $paramsAttribuees
        ]);
    }

    private function convertParamToDisponibilite($dateDebutSemaine, $profil, $creneau, $day)
    {

        $attribuee = new Attribuee();
        $attribuee->setDateDebut($dateDebut);
        $attribuee->setDateFin($dateFin);
        $attribuee->setProfil($profil);

        return $attribuee;
    }

    private function convertDisponibilitesToParams($disponibilites, $dateDebutSemaine)
    {
        $creneaux = [
            ['11H30', '14H30'],
            ['18H30', '19H30'],
            ['19H30', '22H30'],
            ['20H00', '23H00']
        ];

        $data = [];

        foreach ($creneaux as $creneau) {
            $datum = [];
            $creneau_debut = explode('H', $creneau[0]);
            $creneau_fin = explode('H', $creneau[1]);

            for ($day = 0; $day < 7; $day++) {
                $datum[$day] = [];
                $dateDebut = new \DateTime();
                $dateDebut->setTimestamp($dateDebutSemaine->getTimestamp());
                $dateDebut->modify('+' . $day . ' days');
                $dateFin = new \DateTime();
                $dateFin->setTimestamp($dateDebut->getTimestamp());

                $dateDebut->setTime($creneau_debut[0], $creneau_debut[1]);
                $dateFin->setTime($creneau_fin[0], $creneau_fin[1]);

                foreach ($disponibilites as $disponibilite) {
                    if (
                        $this->isBetween($dateDebut, $disponibilite->getDateDebut(), $disponibilite->getDateFin()) &&
                        $this->isBetween($dateFin, $disponibilite->getDateDebut(), $disponibilite->getDateFin())
                    ){
                        $profil = $disponibilite->getProfil();
                        $datum[$day][$profil->getId()] = ['prenom' => $profil->getPrenom(), 'nom' => $profil->getNom(), 'id' => $profil->getId()];
                    }
                }
            }

            $data[$creneau[0] . ' - ' . $creneau[1]] = $datum;
        }

        return $data;
    }

    private function isBetween($sujet, $dateDebut, $dateFin)
    {
        return $sujet >= $dateDebut && $sujet <= $dateFin;
    }

    /**
     * @Route("/factures", name="factures")
     */
    public function factureAction(Request $request)
    {
        $factures = $this->getDoctrine()->getRepository('AppBundle:Facture')->findAll();
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
        
        // replace this example code with whatever you need
        return $this->render('admin/liste_factures.html.twig', [
            "factures" => $params
         ]);
    }

    /**
     * @Route("/facture/ajouter", name="admin_add_facture", defaults={"factureId": null})
     * @Route("/facture/modifier/{factureId}", name="admin_edit_facture")
     */
    public function addFactureAction(Request $request, $factureId)
    {
        $facture;
        if ($factureId === null) {
            $facture = new Facture();
        } else {
            $facture = $this->getDoctrine()
                ->getRepository('AppBundle:Facture')
                ->find($factureId);
            if ($facture === null) {
                $facture = new Facture();
            }
        }
        $facture->setDate(new \DateTime());
        $form = $this->createFactureForm($facture);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($facture);
            $em->flush();

            return $this->redirectToRoute('factures');
        }
        
        return $this->render('admin/add_facture.html.twig', [
            'form' => $form->createView(),
            'isNew' => $factureId === null
        ]);
    }

    private function createFactureForm(Facture $facture)
    {
        $form = $this->createFormBuilder($facture)
            ->add('profil', FormType\ChoiceType::class, [
                'choices' => $this->getDoctrine()->getRepository('AppBundle:Profil')->findIndexed(),
                'label' => 'Livreur'
            ])
            ->add('libelle', FormType\TextType::class, [
                'attr' => ['placeholder' => 'Intitulé de la facture'],
                'label' => 'Intitulé'
            ])
            ->add('montant', FormType\MoneyType::class, [
                'attr' => ['placeholder' => 'Montant en euros'],
                'label' => 'Montant',
                'currency' => null
            ])
            ->getForm();
        ;

        return $form;
    }

    /**
     * @Route("/facture/supprimer", name="delete_facture")
     * @Method({"POST"})
     */
    public function deleteFactureAction(Request $request)
    {
        $facture_id = $request->request->get('facture_id');

        if ($facture_id == null) {
            throw $this->createNotFoundException('Facture does not exist');
        }

        $facture = $this->getDoctrine()->getRepository('AppBundle:Facture')->find($facture_id);
        if ($facture == null) {
            throw $this->createNotFoundException('Facture does not exist');
        }

        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($facture);
        $em->flush();

        return new Response("ok", 200);
    }
    
    /**
     * @Route("/facture/valider", name="validate_facture")
     * @Method({"POST"})
     */
    public function validateFactureAction(Request $request)
    {
        $facture_id = $request->request->get('facture_id');

        if ($facture_id == null) {
            throw $this->createNotFoundException('Facture does not exist');
        }

        $facture = $this->getDoctrine()->getRepository('AppBundle:Facture')->find($facture_id);
        if ($facture == null) {
            throw $this->createNotFoundException('Facture does not exist');
        }

        $facture->setEtat(true);

        $em = $this->getDoctrine()->getEntityManager();
        $em->flush();

        return new Response("ok", 200);
    }
}