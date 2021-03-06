<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints AS Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\DisponibiliteRepository")
 * @ORM\Table(name="attribuee")
 */
class Attribuee
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
    
    /**
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Profil",inversedBy="attribuees")
    * @ORM\JoinColumn(nullable=false)
    */
    private $profil;

    public function getId(){
        return $this->id;
    }

    public function getDateDebut(){
        return $this->dateDebut;
    }

    public function setDateDebut($dateDebut){
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(){
        return $this->dateFin;
    }

    public function setDateFin($dateFin){
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getProfil(){
        return $this->profil;
    }

    public function setProfil(Profil $profil){
        $this->profil = $profil;

        return $this;
    }
}