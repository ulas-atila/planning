<?php
namespace AppBundle\Twig;

use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class StatExtension extends \Twig_Extension
{
    private $entityManager;
    private $token;

    public function __construct(RegistryInterface $doctrine, TokenStorageInterface $token)
    {
        $this->entityManager = $doctrine->getEntityManager();
        $this->token = $token;
    }

    public function getGlobals()
    {
        if ($this->token->getToken() == null || !$this->token->getToken()->getUser() instanceof \AppBundle\Entity\Login) {
            return [];
        } else {
            $login = $this->token->getToken()->getUser();
            if ($login->getAdmin()) {
                return [
                    'nbFacturesAValider' => $this->getNbFacturesAValider(),
                    'nbNouveauMessage' => $this->getNbNouveauMessage(),
                    'nbLivreur' => $this->getNbLivreur()
                ];
            }
        }
        return [
            'nbNouveauMessageUser' => $this->getNbNouveauMessageUser(),
            'nbFacturesUser' => $this->getNbFacturesUser()
        ];
    }

    public function getNbFacturesAValider()
    {
        return $this->entityManager->getRepository('AppBundle:Facture')->countNonValide();
    }

    public function getNbFacturesUser()
    {
        return $this->entityManager->getRepository('AppBundle:Facture')->countUserValide($this->getProfil());
    }

    public function getNbNouveauMessage()
    {
        return $this->entityManager->getRepository('AppBundle:Message')->countAdminNonLu();
    }

    public function getNbNouveauMessageUser()
    {
        return $this->entityManager->getRepository('AppBundle:Message')->countUserNonLu($this->getProfil());
    }

    public function getNbLivreur()
    {
        return $this->entityManager->getRepository('AppBundle:Profil')->count();
    }

    private function getProfil()
    {
        return $this->token->getToken()->getUser()->getProfil();
    }

    public function getName()
    {
        return 'stat_extension';
    }
}