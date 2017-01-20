<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints AS Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="login")
 */
class Login
{
    /**
    * @ORM\Column(type="string",length=40)
    * @ORM\Id
    * @Assert\Email
    */
    private $email;

    /**
    * @ORM\Column(type="string",length=30)
    * @Assert\NotBlank
    */
    private $password;
    
    /**
    * @ORM\Column(type="boolean",options={"default":false})
    */
    private $actif;
    
    /**
    * @ORM\Column(type="boolean",options={"default":false})
    */
    private $admin;
    
    /**
    * @ORM\Column(type="string",length=15)
    * @Assert\NotBlank
    * @Assert\Length(min=15,max=15)
    */
    private $cle;

    /**
    * @ORM\Column(nullable=true)
    * @ORM\OneToOne(targetEntity="AppBundle\Entity\Profil")
    */
    private $profil;

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    public function getActif()
    {
        return $this->actif;
    }

    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    public function getAdmin()
    {
        return $this->admin;
    }

    public function setAdmin($admin)
    {
        $this->admin = $admin;

        return $this;
    }

    public function getCle()
    {
        return $this->cle;
    }

    public function setCle($cle)
    {
        $this->cle = $cle;

        return $this;
    }

    public function getProfil()
    {
        return $this->profil;
    }

    public function setProfil(Profil $profil)
    {
        $this->profil = $profil;

        return $this;
    }
}