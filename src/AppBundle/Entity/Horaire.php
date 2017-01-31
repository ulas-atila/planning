<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints AS Assert;

/**
 * ORM\Entity
 * @ORM\Table(name="horaire")
 */
class Horaire
{
    /**
    * @ORM\Column(type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $id;

    /**
    * @ORM\Column(type="datetime")
    * @Assert\NotBlank
    */
    private $dateDebut;
    
    /**
    * @ORM\Column(type="datetime")
    * @Assert\NotBlank
    */
    private $dateFin;
}