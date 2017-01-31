<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints AS Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="login")
 * @UniqueEntity("email")
 */
class Login implements UserInterface, \Serializable
{
    /**
    * @ORM\Column(type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $id;

    /**
    * @ORM\Column(type="string",length=40, unique=true)
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
    private $admin = false;
    
    /**
    * @ORM\Column(type="boolean",options={"default":false})
    */
    private $limited = false;

    /**
    * @ORM\OneToOne(targetEntity="Profil", inversedBy="login")
    */
    private $profil;

    /**
    * @ORM\Column(type="boolean",options={"default":false})
    */
    private $valide = false;


    public function getValide(){
        return $this->valide;
    }
    
    public function setValide($valide)
    {
        $this->valide = $valide;

        return $this;
    }

    public function getId(){
        return $this->id;
    }
    
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

    public function getAdmin()
    {
        return $this->admin;
    }

    public function setAdmin($admin)
    {
        $this->admin = $admin;

        return $this;
    }

    public function getLimited()
    {
        return $this->limited;
    }

    public function setLimited($limited)
    {
        $this->limited = $limited;

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

    public function getRoles()
    {
        if (!$this->getValide()) {
            return [];
        }
        if ($this->getAdmin()) {
            if ($this->limited) {
                return ['ROLE_ADMIN'];
            } else {
                return ['ROLE_ADMIN', 'ROLE_ADMIN_VALIDATE'];
            }
        } else if ($this->getProfil() && $this->getProfil()->getActive()) {
            return ['ROLE_USER'];
        }

        return [];
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
        ));
    }

    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->email,
            $this->password,
        ) = unserialize($serialized);
    }
}