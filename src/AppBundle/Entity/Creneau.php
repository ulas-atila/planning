<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints AS Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\CreneauRepository")
 * @ORM\Table(name="creneau")
 */
class Creneau
{
    /**
    * @ORM\Column(type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $id;

    /**
    * @ORM\Column(type="integer")
    * @Assert\NotBlank
    * @Assert\Range(
    *      min = 0,
    *      max = 23,
    *      minMessage = "La valeur doit etre entre 0 et 23",
    *      maxMessage = "La valeur doit etre entre 0 et 23"
    * )
    */
    private $heureDebut;

    /**
    * @ORM\Column(type="integer")
    * @Assert\NotBlank
    * @Assert\Range(
    *      min = 0,
    *      max = 59,
    *      minMessage = "La valeur doit etre entre 0 et 59",
    *      maxMessage = "La valeur doit etre entre 0 et 59"
    * )
    */
    private $minuteDebut;

    /**
    * @ORM\Column(type="integer")
    * @Assert\NotBlank
    * @Assert\Range(
    *      min = 0,
    *      max = 23,
    *      minMessage = "La valeur doit etre entre 0 et 23",
    *      maxMessage = "La valeur doit etre entre 0 et 23"
    * )
    */
    private $heureFin;

    /**
    * @ORM\Column(type="integer")
    * @Assert\NotBlank
    * @Assert\Range(
    *      min = 0,
    *      max = 59,
    *      minMessage = "La valeur doit etre entre 0 et 59",
    *      maxMessage = "La valeur doit etre entre 0 et 59"
    * )
    */
    private $minuteFin;

    public function getId(){
        return $this->id;
    }

    public function getHeureDebut(){
        return $this->heureDebut;
    }

    public function setHeureDebut($heureDebut){
        $this->heureDebut = $heureDebut;

        return $this;
    }

    public function getMinuteDebut(){
        return $this->minuteDebut;
    }

    public function setMinuteDebut($minuteDebut){
        $this->minuteDebut = $minuteDebut;

        return $this;
    }

    public function getHeureFin(){
        return $this->heureFin;
    }

    public function setHeureFin($heureFin){
        $this->heureFin = $heureFin;

        return $this;
    }

    public function getMinuteFin(){
        return $this->minuteFin;
    }

    public function setMinuteFin($minuteFin){
        $this->minuteFin = $minuteFin;

        return $this;
    }

}