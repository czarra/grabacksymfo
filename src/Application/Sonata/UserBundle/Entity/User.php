<?php

namespace App\Application\Sonata\UserBundle\Entity;

use Sonata\UserBundle\Entity\BaseUser as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * This file has been generated by the SonataEasyExtendsBundle.
 *
 * @link https://sonata-project.org/easy-extends
 *
 * References:
 * @link http://www.doctrine-project.org/projects/orm/2.0/docs/reference/working-with-objects/en
 */

/**
 * Class User
 * @package App\Application\Sonata\UserBundle\Entity
 *
 * @ORM\Table(name="fos_user_usersssss")
 * @ORM\Entity()
 */
class User extends BaseUser
{
    /**
     * @var integer $id
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $apiToken;
    
   
    
    /**
     * Get id
     *
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function __construct()
    {
        parent::__construct();
        $this->roles = array('ROLE_USER');
        $this->apiToken = md5(time().$this->createUnic());
    }
    private function createUnic()
    {
        $string = md5(uniqid(rand(), true));
        return substr($string, 0, 3);
    }

    public function __toString()
    {
        return (string) $this->getUsername();
    }
    
    public function getApiToken(){
        return $this->apiToken;
    }
    
    public function setApiToken($apiToken){
        $this->apiToken=$apiToken;
    }
}
