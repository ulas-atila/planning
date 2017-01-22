<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints AS Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="facture")
 */
class Facture
{
    /**
    * @ORM\Column(type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $id;

    /**
    * @ORM\Column(type="date")
    * @Assert\NotBlank
    */
    private $date;
    
    /**
    * @ORM\Column(type="float")
    * @Assert\NotBlank
    */
    private $montant;
    
    /**
    * @ORM\Column(type="boolean",options={"default":false})
    */
    private $etat = false;
    
    /**
    * @ORM\Column(type="string",length=200)
    * @Assert\NotBlank
    */
    private $libelle;

    /**
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Profil",inversedBy="factures")
    * @ORM\JoinColumn(nullable=false)
    */
    private $profil;

    public function getId(){
        return $this->id;
    }

    public function getDate(){
        return $this->date;
    }

    public function setDate($date){
        $this->date = $date;

        return $this;
    }

    public function getMontant(){
        return $this->montant;
    }

    public function setMontant($montant){
        $this->montant = $montant;

        return $this;
    }

    public function getEtat(){
        return $this->etat;
    }

    public function setEtat($etat){
        $this->etat = $etat;

        return $this;
    }

    public function getLibelle(){
        return $this->libelle;
    }

    public function setLibelle($libelle){
        $this->libelle = $libelle;

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