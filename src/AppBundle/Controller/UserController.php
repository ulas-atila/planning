<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Entity\Disponibilite;
use AppBundle\Entity\Profil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type as FormType;

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
     * @Route("/profil", name="edit_profil")
     */
    public function addLivreurAction(Request $request)
    {
        $login = $this->getUser();
        $profil = $login->getProfil();
        $previousPhoto = $profil->getPhoto();
        $previousEmail = $profil->getEmail();
        $profil->setDateentree(new \DateTime());
        $form = $this->createProfilForm($profil, false);

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
            
            $em->persist($profil);
            $password = $form->get('password')->getData();
            if($password!= "")
            {
                $login = $em->getRepository('AppBundle:Login')->findOneByProfil($profil);
                $login->setPassword($password);
            }
            $profil->setEmail($previousEmail);
            $em->flush();

            return $this->redirectToRoute('admin_list');
        }
        
        // replace this example code with whatever you need
        return $this->render('user/profil.html.twig', [
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
            ->add('datedenaissance', FormType\BirthdayType::class, [
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
            ]);
            if (!$isNew) {
                $form->add('password', FormType\PasswordType::class, [
                    'mapped' => false,
                    'attr' => ['placeholder' => 'Mot de passe'],
                    'label' => 'Mot de passe'
                ]);
            }
            $form = $form->getForm();
        ;

        return $form;
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

        $login = $this->getUser();
        $profil = $login->getProfil();
        $disponibilites = $this->getDoctrine()->getRepository('AppBundle:Disponibilite')->findForUser($profil, $dateDebut, $dateFin);
        $params = $this->convertDisponibilitesToParams($disponibilites);

        if ($request->request->get('edit')) {
            $em = $this->getDoctrine()->getEntityManager();
            foreach ($params as $param => $disponibilite) {
                if ($request->request->get($param) === null) {
                    $em->remove($disponibilite);
                }
            }
            foreach ($request->request->all() as $key => $value) {
                if ($key == "jour" || $key == "edit") {
                    continue;
                }
                if (!array_key_exists($key, $params)) {
                    $em->persist($this->convertParamToDisponibilite($dateDebut, $key, $profil));
                }
            }
            $em->flush();
            $params = $request->request->all();
        }

        return $this->render('user/planning.html.twig', [
            "date" => $date,
            "disponibilites" => $params
        ]);
    }

    private function convertDisponibilitesToParams($disponibilites)
    {
        $midi_debut = $this->getParameter('creneau_midi_debut');
        $midi_fin = $this->getParameter('creneau_midi_fin');
        $soir_debut = $this->getParameter('creneau_soir_debut');
        $soir_fin = $this->getParameter('creneau_soir_fin');
        $jours = ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
        $params = [];
        foreach ($disponibilites as $disponibilite) {
            $dateDebut = $disponibilite->getDateDebut();
            $heureDebut = $dateDebut->format('H:i');
            $dateFin = $disponibilite->getDateFin();
            $heureFin = $dateDebut->format('H:i');
            if ($this->isBetween($heureDebut, $midi_debut, $midi_fin) && $this->isBetween($heureFin, $midi_debut, $midi_fin)) {
                $creneau = "midi";
            } elseif ($this->isBetween($heureDebut, $soir_debut, $soir_fin) && $this->isBetween($heureFin, $soir_debut, $soir_fin)){
                $creneau = "soir";
            } else {
                continue;
            }

            $day = date('w', $dateDebut->getTimestamp());
            $key = $jours[$day] . '_' . $creneau;

            $params[$key] = $disponibilite;
        }

        return $params;
    }

    private function convertParamToDisponibilite($dateDebutSemaine, $param, $profil)
    {
        list($jour, $creneau) = explode('_', $param);

        $creneau_debut = explode(':', $this->getParameter('creneau_' . $creneau . '_debut'));
        $creneau_fin = explode(':', $this->getParameter('creneau_' . $creneau . '_fin'));

        $jours = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];
        $daysToAdd = array_search($jour, $jours);

        $dateDebut = new \DateTime();
        $dateDebut->setTimestamp($dateDebutSemaine->getTimestamp());
        $dateDebut->modify('+' . $daysToAdd . ' days');
        $dateDebut->setTime($creneau_debut[0], $creneau_debut[1]);

        $dateFin = new \DateTime();
        $dateFin->setTimestamp($dateDebutSemaine->getTimestamp());
        $dateFin->modify('+' . $daysToAdd . ' days');
        $dateFin->setTime($creneau_fin[0], $creneau_fin[1]);

        $disponibilite = new Disponibilite();
        $disponibilite->setDateDebut($dateDebut);
        $disponibilite->setDateFin($dateFin);
        $disponibilite->setProfil($profil);

        return $disponibilite;
    }

    private function isBetween($sujet, $heureDebut, $heureFin)
    {
        return strtotime($sujet) >= strtotime($heureDebut) && strtotime($sujet) <= strtotime($heureFin);
    }

    /**
     * @Route("/factures", name="user_factures")
     */
    public function factureAction(Request $request)
    {
        $dateDebut = $request->request->get('date_debut');
        $dateFin = new \DateTime($request->request->get('date_fin'));
        if (null === $dateDebut) {
            $dateDebut = new \DateTime();
            $dateDebut->setTimestamp($dateFin->getTimestamp());
            $dateDebut->modify('-1 month');
        } else {
            $dateDebut = new \DateTime($dateDebut);
        }
        if ($dateDebut > $dateFin) {
            $tmp = $dateDebut;
            $dateDebut = $dateFin;
            $dateFin = $tmp;
        }
        $login = $this->getUser();
        $profil = $login->getProfil();
        $factures = $this->getDoctrine()->getRepository('AppBundle:Facture')->findForUser($profil, $dateDebut, $dateFin);
        $params = [];
        $allIds = [];
        foreach ($factures as $facture) {
            $allIds[] = $facture->getId();
            $params[] = [
                "id" => $facture->getId(),
                "date" => $facture->getDate(),
                "montant" => $facture->getMontant(),
                "etat" => $facture->getEtat(),
                "libelle" => $facture->getLibelle(),
                "profil" => $facture->getProfil()->getNom() . " " . $facture->getProfil()->getPrenom()
            ];
        }

        return $this->render('user/factures.html.twig', [
            "factures" => $params,
            "dateDebut" => $dateDebut,
            "dateFin" => $dateFin,
            "allIds" => implode('-', $allIds)
         ]);
    }

    /**
     * @Route("/factures/facture{ids}.pdf", name="pdf_facture")
     */
    public function facturePDFAction(Request $request, $ids)
    {
        set_time_limit(0);
        $factureIds = explode('-', $ids);
        $factures = $this->getDoctrine()->getRepository('AppBundle:Facture')->findById($factureIds);
        if (!isset($factures[0])) {
            throw $this->createNotFoundException('Factures does not exist');
        }
        $profil = $this->getUser()->getProfil();

        $params = [];
        foreach ($factures as $facture) {
            $factProfile = $facture->getProfil();
            if ($facture->getProfil() !== $profil || $facture->getEtat() === false) {
                throw $this->createNotFoundException('Factures does not exist');
            }
            $params[] = [
                "facture_id" => $facture->getId(),
                "facture_montant" => $facture->getMontant(),
                "facture_libelle" => $facture->getLibelle(),
                "livreur_nom" => $profil->getNom(),
                "livreur_prenom" =>$profil->getPrenom(),
                "livreur_siret" => $profil->getSiret(),
                "livreur_adresse" => $profil->getAdresse()
            ];
        }

        $file = 'Facture' . $ids . '.pdf';
        $html = $this->renderView('user/facture.html.twig', [
           "informations" => $params
        ]);
        $html2pdf = new \Html2Pdf_Html2Pdf('P','A4','fr');
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($html);
        $html2pdf->Output($file);

        return new Response('', 200, ['Content-Type' => 'application/pdf']);
    }

}
