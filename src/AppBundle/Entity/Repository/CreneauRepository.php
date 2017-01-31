<?php
namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class CreneauRepository extends EntityRepository
{
    public function findFomatted()
    {
        $data = $this->findBy([],['heureDebut' => 'ASC']);
        $creneaux = [];
        foreach ($data as $datum) {
            $creneaux[] = [
                sprintf("%'.02d", $datum->getHeureDebut()) . 'H' . sprintf("%'.02d", $datum->getMinuteDebut()),
                sprintf("%'.02d", $datum->getHeureFin()) . 'H' . sprintf("%'.02d", $datum->getMinuteFin())
            ];
        }

        return $creneaux;
    }
}
