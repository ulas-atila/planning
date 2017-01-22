<?php
namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class FactureRepository extends EntityRepository
{
    public function countNonValide()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('COUNT(f)')
            ->from('AppBundle:Facture', 'f')
            ->where('f.etat = false');
        return intval($qb->getQuery()->execute()[0][1]);
    }

    public function countUserValide($profil)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('COUNT(f)')
            ->from('AppBundle:Facture', 'f')
            ->where('f.etat = true')
            ->andWhere('f.profil = ?1')
            ->setParameter(1, $profil);
        return intval($qb->getQuery()->execute()[0][1]);
    }
}