<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User
 *
 * @author rad
 */
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="email", type="string", unique=true)
     */
    private $email;

    /**
     * @ORM\Column(name="password", type="string", nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(name="roles", type="json_array")
     */
    private $roles;

     public function getId(){
        return $this->id;
    }
    
    public function getRoles(){
        return $this->roles;
    }
    
    public function getPassword(){
        return $this->password;
    }

    public function getUsername(){
        return $this->email;
    }
    
    public function getSalt(){
        return 0;
    }
    
    public function eraseCredentials(){
        
    }
}