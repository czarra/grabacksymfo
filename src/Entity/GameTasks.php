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
 * @ORM\Entity(repositoryClass="App\Repository\GamesTasksRepository")
 * @ORM\Table(name="game_tasks",uniqueConstraints={@ORM\UniqueConstraint(name="gametask", columns={"game_id", "task_id"})})
 */
class GameTasks
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Tasks")
     * @ORM\JoinColumn(name="task_id", referencedColumnName="id")
     */
    private $task;
    
    /**
     * @ORM\Column(type="integer")
     */
    private $sequence;
  

    public function __construct()
    {
       
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

    public function getTask()
    {
        return $this->task;
    }
    
    public function setTask($task)
    {
        return $this->task=$task;
    }
    
    public function getSequence()
    {
        return $this->sequence;
    }
    
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;
    }
   
    
    public function __toString() {
        return "Gra : ".$this->game ." Zadnie : ". $this->task ;
    }
    
  
}