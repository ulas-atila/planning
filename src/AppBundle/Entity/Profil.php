<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints AS Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="profil")
 */
class Profil
{
    /**
    * @ORM\Column(type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $id;

    /**
    * @ORM\Column(type="string",length=30)
    * @Assert\NotBlank
    */
    private $nom;
    
    /**
    * @ORM\Column(type="string",length=30)
    * @Assert\NotBlank
    */
    private $prenom;
    
    /**
    * @ORM\Column(type="date")
    * @Assert\NotBlank
    */
    private $datedenaissance;
    
    /**
    * @ORM\Column(type="string",length=200)
    * @Assert\NotBlank
    */
    private $adresse;
    
    /**
    * @ORM\Column(type="string",length=50)
    * @Assert\NotBlank
    * @Assert\Email
    */
    private $email;
    
    /**
    * @ORM\Column(type="string",length=30)
    * @Assert\NotBlank
    */
    private $telephone;
    
    /**
    * @ORM\Column(type="string",length=14)
    * @Assert\NotBlank
    * @Assert\Length(min=14,max=14)    
    */
    private $siret;
    
    /**
    * @ORM\Column(type="string",length=40)
    * @Assert\NotBlank
    */
    private $villedelivraison;
    
    /**
    * @ORM\Column(type="date")
    * @Assert\NotBlank
    */
    private $dateentree;

    /**
    * @ORM\Column(type="string",length=30)
    * @Assert\NotBlank
    */
    private $photo;

    /**
    * @ORM\Column(type="string",length=23)
    * @Assert\NotBlank
    * @Assert\Length(min=23,max=23)
    */
    private $rib;

    /**
    * @ORM\OneToMany(targetEntity="AppBundle\Entity\Facture",mappedBy="profil")
    */
    private $factures;

    /**
    * ORM\OneToMany(targetEntity="AppBundle\Entity\Horaire",mappedBy="profil")
    */
    private $disponibilite;

    /**
    * ORM\OneToMany(targetEntity="AppBundle\Entity\Horaire",mappedBy="profil")
    */
    private $heuresAttribuees;

    public function getId(){
        return $this->id;
    }

    public function getNom(){
        return $this->nom;
    }

    public function setNom($nom){
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(){
        return $this->prenom;
    }

    public function setPrenom($prenom){
        $this->prenom = $prenom;

        return $this;
    }

    public function getDatedenaissance(){
        return $this->datedenaissance;
    }

    public function setDatedenaissance($datedenaissance){
        $this->datedenaissance = $datedenaissance;

        return $this;
    }

    public function getAdresse(){
        return $this->adresse;
    }

    public function setAdresse($adresse){
        $this->adresse = $adresse;

        return $this;
    }

    public function getEmail(){
        return $this->email;
    }

    public function setEmail($email){
        $this->email = $email;

        return $this;
    }

    public function getTelephone(){
        return $this->telephone;
    }

    public function setTelephone($telephone){
        $this->telephone = $telephone;

        return $this;
    }

    public function getSiret(){
        return $this->siret;
    }

    public function setSiret($siret){
        $this->siret = $siret;

        return $this;
    }

    public function getVilledelivraison(){
        return $this->villedelivraison;
    }

    public function setVilledelivraison($villedelivraison){
        $this->villedelivraison = $villedelivraison;

        return $this;
    }

    public function getDateentree(){
        return $this->dateentree;
    }

    public function setDateentree($dateentree){
        $this->dateentree = $dateentree;

        return $this;
    }

    public function getPhoto(){
        return $this->photo;
    }

    public function setPhoto($photo){
        $this->photo = $photo;

        return $this;
    }

    public function getRib(){
        return $this->rib;
    }

    public function setRib($rib){
        $this->rib = $rib;

        return $this;
    }

    public function getFactures(){
        return $this->factures;
    }

    public function getLogin(){
        return $this->login;
    }

    public function setLogin(Login $login){
        $this->login = $login;

        return $this;
    }
}