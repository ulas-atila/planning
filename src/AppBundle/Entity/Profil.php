<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints AS Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="profil")
 * @UniqueEntity("email")
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
    * @Assert\NotBlank(message="Valeur obligatoire")
    */
    private $nom;

    /**
    * @ORM\Column(type="string",length=30)
    * @Assert\NotBlank(message="Valeur obligatoire")
    */
    private $prenom;
    
    /**
    * @ORM\Column(type="date")
    * @Assert\NotBlank(message="Valeur obligatoire")
    */
    private $datedenaissance;
    
    /**
    * @ORM\Column(type="string",length=200)
    * @Assert\NotBlank(message="Valeur obligatoire")
    */
    private $adresse;
    
    /**
    * @ORM\Column(type="string",length=50, unique=true)
    * @Assert\NotBlank(message="Valeur obligatoire")
    * @Assert\Email
    */
    private $email;
    
    /**
    * @ORM\Column(type="string",length=30)
    * @Assert\NotBlank(message="Valeur obligatoire")
    */
    private $telephone;
    
    /**
    * @ORM\Column(type="string",length=14)
    * @Assert\Regex(pattern="/^[0-9]{14}$/", message="Format incorrect") 
    */
    private $siret;
    
    /**
    * @ORM\Column(type="string",length=40)
    * @Assert\NotBlank(message="Valeur obligatoire")
    */
    private $villedelivraison;
    
    /**
    * @ORM\Column(type="date")
    * @Assert\NotBlank(message="Valeur obligatoire")
    */
    private $dateentree;

    /**
    * @ORM\Column(type="string",length=150, nullable=true)
    * @Assert\File(mimeTypes={ "image/gif", "image/jpeg", "image/png" }, maxSize="500k")
    */
    private $photo;

    /**
    * @ORM\Column(type="string",length=23)
    */
    private $rib;

    /**
    * @ORM\OneToMany(targetEntity="AppBundle\Entity\Facture",mappedBy="profil")
    */
    private $factures;
    
    /**
    * @ORM\Column(type="boolean",options={"default":true})
    */
    private $active = true;

    /**
    * ORM\OneToMany(targetEntity="AppBundle\Entity\Disponibilite",mappedBy="profil")
    */
    private $disponibilites;

    /**
    * ORM\OneToMany(targetEntity="AppBundle\Entity\Attribuee",mappedBy="profil")
    */
    private $attribuees;

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

    public function getActive(){
        return $this->active;
    }

    public function setActive($active){
        $this->active = $active;

        return $this;
    }

    public function getRib(){
        return $this->rib;
    }

    public function setRib($rib){
        $this->rib = str_replace(' ', '', $rib);

        return $this;
    }

    public function getFactures(){
        return $this->factures;
    }

    public function getDisponibilites(){
        return $this->disponibilites;
    }

    public function getAttribuees(){
        return $this->attribuees;
    }

    /**
     * @Assert\Callback
     */
    public function validateRIB(ExecutionContextInterface $context, $payload)
    {
        if (!$this->isValidRib($this->rib)) {
            $context->buildViolation('Format incorrect!')
                ->atPath('rib')
                ->addViolation();
        }
    }
    private function isValidRib($rib)
    {
        if(mb_strlen($rib) !== 23)
        {
            return false;
        }
        $key = substr($rib,-2);
        $bank = substr($rib,0,5);
        $bank = substr($rib,0,5);
        $branch = substr($rib,5,5);
        $account = substr($rib,10,11);
        $account = strtr($account,
        'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        '12345678912345678923456789');
        return 97 - bcmod(89*$bank + 15 * $branch + 3 * $account,97) === (int)$key;
    }
}