<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Games
 *
 * @author rad
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TasksRepository")
 */
class Tasks
{
    const MAX_DISTANCE = 0.001;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=35, unique=true )
     */
    private $name;
    
    /**
     * @ORM\Column(type="float")
     */
    private $longitude;
    
    /**
     * @ORM\Column(type="float")
     */
    private $latitude;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    public function __construct()
    {

    }

    public function getId()
    {
        return $this->id;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function getLongitude()
    {
        return $this->longitude;
    }
    
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }
    
    public function getLatitude()
    {
        return $this->latitude;
    }
    
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }
    public function getDescription()
    {
        return $this->description;
    }
    
    public function setDescription($description)
    {
        $this->description = $description;
    }
    
    public function checkifGoodPlace($longitude,$latitude): bool{
        if(is_numeric($longitude) && is_numeric($latitude)){
            $a = $longitude - $this->longitude;
            $b = $latitude - $this->latitude;  
            $distance =sqrt(pow(($a), 2)+pow(($b), 2));
            if($distance<self::MAX_DISTANCE){
                return true;
            }
        }
        return false;
    }


    public function __toString() {
        return "Zadanie : ".$this->name ;
    }
}