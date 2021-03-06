<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints AS Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\MessageRepository")
 * @ORM\Table(name="message")
 */
class Message
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
    private $date;

    /**
    * @ORM\Column(type="text")
    * @Assert\NotBlank
    */
    private $message;
    
    /**
    * @ORM\Column(type="boolean",options={"default":false})
    * @Assert\NotBlank
    */
    private $vu = false;

    /**
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Profil")
    * @ORM\JoinColumn(nullable=false)
    */
    private $profil;

    /**
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Login")
    * @ORM\JoinColumn(nullable=false)
    */
    private $login;

    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;

        return $this;
    }

    public function getDate(){
        return $this->date;
    }

    public function setDate($date){
        $this->date = $date;

        return $this;
    }

    public function getMessage(){
        return $this->message;
    }

    public function setMessage($message){
        $this->message = $message;

        return $this;
    }

    public function getVu(){
        return $this->vu;
    }

    public function setVu($vu){
        $this->vu = $vu;

        return $this;
    }

    public function getProfil(){
        return $this->profil;
    }

    public function setProfil(Profil $profil){
        $this->profil = $profil;

        return $this;
    }

    public function getLogin(){
        return $this->login;
    }

    public function setLogin(Login $login){
        $this->login = $login;

        return $this;
    }
}