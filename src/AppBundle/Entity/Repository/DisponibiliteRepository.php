<?php
namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class DisponibiliteRepository extends EntityRepository
{
    public function findForUser($profil, $dateDebut, $dateFin)
    {
        $qb = $this->createQueryBuilder('d');
        $qb
            ->where('d.profil = ?1')
            ->andWhere('d.dateDebut BETWEEN ?2 AND ?3')
            ->setParameter(1, $profil)
            ->setParameter(2, $dateDebut)
            ->setParameter(3, $dateFin);

        return $qb->getQuery()->execute();
    }

    public function findForAdmin($dateDebut, $dateFin)
    {
        $qb = $this->createQueryBuilder('d');
        $qb
            ->where('d.dateDebut BETWEEN ?1 AND ?2')
            ->setParameter(1, $dateDebut)
            ->setParameter(2, $dateFin);

        return $qb->getQuery()->execute();
    }
}