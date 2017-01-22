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
}