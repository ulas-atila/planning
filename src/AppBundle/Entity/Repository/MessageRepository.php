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
            ->orderBy('m.date', 'ASC');

        return $qb->getQuery()->execute();
    }

    public function findByUserId($userId)
    {
        $qb = $this->createQueryBuilder('m');
        $qb
            ->innerJoin("m.profil", "p")
            ->where('p.id = ?1')
            ->orderBy('m.date')
            ->setParameter(1, $userId);

        return $qb->getQuery()->execute();
    }
}