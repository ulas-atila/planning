<?php
namespace AppBundle\Twig;

use Symfony\Bridge\Doctrine\RegistryInterface;

class StatExtension extends \Twig_Extension
{
    private $entityManager;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->entityManager = $doctrine->getEntityManager();
    }

    public function getGlobals()
    {
        return [
            'nbFacturesAValider' => $this->getNbFacturesAValider(),
            'nbNouveauMessage' => $this->getNbNouveauMessage(),
            'nbLivreur' => $this->getNbLivreur()
        ];
    }

    public function getNbFacturesAValider()
    {
        return $this->entityManager->getRepository('AppBundle:Facture')->countNonValide();
    }

    public function getNbNouveauMessage()
    {
        return $this->entityManager->getRepository('AppBundle:Message')->countAdminNonLu();
    }

    public function getNbLivreur()
    {
        return $this->entityManager->getRepository('AppBundle:Profil')->count();
    }

    public function getName()
    {
        return 'stat_extension';
    }
}