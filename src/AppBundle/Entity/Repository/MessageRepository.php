<?php
namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class MessageRepository extends EntityRepository
{
    public function findLasts()
    {
        $qb = $this->createQueryBuilder('m');
        $qb
            ->where(
                $qb->expr()->in(
                    "m.date",
                    "SELECT MAX(m2.date) FROM AppBundle:Message m2 WHERE m2.profil=m.profil"
                )
            )
            ->orderBy('m.date', 'DESC');

        return $qb->getQuery()->execute();
    }

    public function findByUserId($userId)
    {
        $qb = $this->createQueryBuilder('m');
        $qb
            ->innerJoin("m.profil", "p")
            ->where('p.id = ?1')
            ->orderBy('m.date', 'ASC')
            ->setParameter(1, $userId);

        return $qb->getQuery()->execute();
    }

    public function countAdminNonLu()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('COUNT(m)')
            ->from('AppBundle:Message', 'm')
            ->innerJoin('m.login', 'l')
            ->where('m.vu = false')
            ->andWhere('l.admin = false');
        return intval($qb->getQuery()->execute()[0][1]);
    }

    public function countUserNonLu($profil)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('COUNT(m)')
            ->from('AppBundle:Message', 'm')
            ->innerJoin('m.login', 'l')
            ->where('m.vu = false')
            ->andWhere('m.profil = ?1')
            ->andWhere('l.admin = true')
            ->setParameter(1, $profil);
        return intval($qb->getQuery()->execute()[0][1]);
    }
}