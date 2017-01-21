<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
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
}
