<?php
namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class ProfilRepository extends EntityRepository
{
    public function findIndexed()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('p')
            ->addSelect('CONCAT(CONCAT(CONCAT(CONCAT(p.prenom, \' \'), p.nom), \' \'), p.email)')
            ->from('AppBundle:Profil', 'p');
        $iteration = $qb->getQuery()->execute();
        $result = [];
        foreach ($iteration as $row) {
            $result[$row[1]] = $row[0];
        }

        return $result;
    }

    public function count()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('COUNT(p)')
            ->from('AppBundle:Profil', 'p')
            ->where('p.active = true');
        return intval($qb->getQuery()->execute()[0][1]);
    }
}