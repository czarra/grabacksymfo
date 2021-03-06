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
 * @ORM\Entity(repositoryClass="App\Repository\UserGameTaskRepository")
 * @ORM\Table(name="user_game_task",uniqueConstraints={@ORM\UniqueConstraint(name="user_game_task", columns={"game_task_id", "user_id"})})
 */
class UserGameTask
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\GameTasks")
     * @ORM\JoinColumn(name="game_task_id", referencedColumnName="id")
     */
    private $gameTask;
    
   
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
    
    public function getGameTask()
    {
        return $this->gameTask;
    }
    
    public function setGameTask($gameTask)
    {
        return $this->gameTask=$gameTask;
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
   
    
    public function __toString() {
        return "Użytkownik : ".$this->user ." Zadnie : ". $this->gameTask ;
    }
    
  
}