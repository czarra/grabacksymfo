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
 * @ORM\Entity(repositoryClass="App\Repository\UserGameRepository")
 * @ORM\Table(name="user_game",uniqueConstraints={@ORM\UniqueConstraint(name="user_game_task", columns={"game_id", "user_id"})})
 */
class UserGame
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Games")
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id")
     */
    private $game;
    
   
    /**
     * @ORM\ManyToOne(targetEntity="App\Application\Sonata\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    
    /**
     * @ORM\Column(type="datetime")
     */
    private $timeStart;
    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $timeStop;
  

    public function __construct()
    {
       $this->timeStart = new \DateTime("now");
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getGame()
    {
        return $this->game;
    }
    
    public function setGame($game)
    {
        return $this->game=$game;
    }

    public function getUser()
    {
        return $this->user;
    }
    
    public function setUser($user)
    {
        return $this->user=$user;
    }
    
    public function getTimeStart()
    {
        return $this->timeStart;
    }
    
    public function setTimeStart($timeStart)
    {
        $this->timeStart = $timeStart;
    }
    
     public function getTimeStop()
    {
        return $this->timeStop;
    }
    
    public function setTimeStop()
    {
        $this->timeStop = new \DateTime("now");
    }
    
    public function getTime(){
        $allTime = "";
        if(!is_null($this->timeStop)){
            $diff = $this->timeStop->diff($this->timeStart);
            $years = $diff->format("%y");
            $month = $diff->format("%m");
            $days = $diff->format("%d");
            $time = $diff->format("%H:%I:%S");
            $allTime = "";
            if(!empty($years)){
               $allTime .= $years."y ";
            }
            if(!empty($month)){
               $allTime .= $month."m ";
            }
            if(!empty($days)){
               $allTime .= $days."d ";
            }
            if(!empty($time)){
               $allTime .= $time."h ";
            }
            //var_dump(date_diff($this->timeStop,$this->timeStart));
        }
        return $allTime;
    }

    public function __toString() {
        return "Użytkownik : ".$this->user ." Zadnie : ". $this->game;
    }
    
  
}