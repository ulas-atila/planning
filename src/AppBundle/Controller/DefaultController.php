<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Profil;
use AppBundle\Entity\Login;
use Symfony\Component\Form\Extension\Core\Type as FormType;


class DefaultController extends Controller
{
    

    /**
     * @Route("/inscription", name="inscription")
     */
    public function inscriptionAction(Request $request)
    {
        $profil = new Profil();
        $profil->setDateentree(new \DateTime()); 
        $form = $this->createProfilForm($profil);
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
            $login = new Login();
                $login->setEmail($profil->getEmail());
                $login->setPassword($form->get('password')->getData());
                $login->setProfil($profil);
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($login);
                $em->persist($profil);
                $em->flush();
                $message = \Swift_Message::newInstance()
                    ->setSubject('Inscription')
                    ->setFrom($this->getParameter('admin_mail'))
                    ->setTo($profil->getEmail())
                    ->setBody(
                        $this->renderView(
                            'default/inscription_email.html.twig',
                            array(
                                'nom' => $profil->getNom(),
                                'prenom' => $profil->getPrenom()
                            )
                        ),
                        'text/html'
                    )
                ;
                $this->get('mailer')->send($message);
                return $this->redirectToRoute('post_inscription');
        }

         return $this->render('default/inscription.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/postinscription", name="post_inscription")
     */
    public function postinscriptionAction()
    {
        return $this->render('default/post_inscription.html.twig');
    }

    /**
     * @Route("/postpassword", name="post_password")
     */
    public function postpasswordAction()
    {
        return $this->render('default/post_password.html.twig');
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
                $form->add('password', FormType\PasswordType::class, [
                    'mapped' => false,
                    'required' => true,
                    'attr' => ['placeholder' => 'Mot de passe'],
                    'label' => 'Mot de passe'
                ]);
            $form = $form->getForm();
        ;

        return $form;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $checker = $this->container->get('security.authorization_checker');
        if ($checker->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin_homepage');
        } else if ($checker->isGranted('ROLE_USER')){
            return $this->redirectToRoute('user_homepage');
        }
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('default/index.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    /**
     * @Route("/mot-de-passe-oublie", name="forget_password")
     */
    public function passwordAction(Request $request)
    {
        // last username entered by the user
        $lastUsername = $request->request->get('_username');
        $error = null;
        if ($lastUsername) {
            $login = $this->getDoctrine()->getRepository('AppBundle:Login')->findOneByEmail($lastUsername);
            if ($login === null || $login->getAdmin()) {
                $error = "L'email n'existe pas.";
            } else {
                $profil = $login->getProfil();
                $message = \Swift_Message::newInstance()
                    ->setSubject('Inscription')
                    ->setFrom($this->getParameter('admin_mail'))
                    ->setTo($profil->getEmail())
                    ->setBody(
                        $this->renderView(
                            'default/inscription_email.html.twig',
                            array(
                                'nom' => $profil->getNom(),
                                'prenom' => $profil->getPrenom(),
                                'password' => $login->getPassword()
                            )
                        ),
                        'text/html'
                    )
                ;
                $this->get('mailer')->send($message);
                return $this->redirectToRoute('post_password');
            }
        }

        return $this->render('default/password.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }
}
