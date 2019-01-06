<?php
// src/Entity/User.php

namespace App\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
        $this->roles = array('ROLE_USER');
        $this->salt= $this->createSalt();
        $this->apiToken = md5(time().$this->salt);
    }
    
    private function createSalt()
    {
        $string = md5(uniqid(rand(), true));
        return substr($string, 0, 3);
    }
    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $apiToken;
    
    public function getApiToken(){
        return $this->apiToken;
    }
    
    public function setApiToken($apiToken){
        $this->apiToken=$apiToken;
    }
    
    public function __toString() {
        return $this->username;
    }
}