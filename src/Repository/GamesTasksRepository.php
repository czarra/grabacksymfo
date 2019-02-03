<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GamesRepository
 *
 * @author rad
 */
namespace App\Repository;

use App\Entity\GameTasks;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Application\Sonata\UserBundle\Entity\User;
use App\Entity\Games;
use App\Entity\UserGameTask;

class GamesTasksRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GameTasks::class);
    }

    
    public function findAllTaskByGameAndUser(Games $game, User $user)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('g_t')
            ->from(UserGameTask::class, 'u_g_t')
            ->leftJoin(
            GameTasks::class, 'g_t',
            \Doctrine\ORM\Query\Expr\Join::WITH,
            'u_g_t.gameTask = g_t'
            )
            ->where("g_t.game=".$game->getId()." and u_g_t.user=".$user->getId())
            ->orderBy("g_t.sequence","ASC")   ;

        return $qb->getQuery()->getResult();
    }
    
    public function findCurrentTaskByGameAndUser(Games $game, User $user)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('g_t')
            ->from(UserGameTask::class, 'u_g_t')
            ->leftJoin(
            GameTasks::class, 'g_t',
            \Doctrine\ORM\Query\Expr\Join::WITH,
            'u_g_t.gameTask = g_t'
            )
            ->where("g_t.game=".$game->getId()." "
                    . "AND u_g_t.user=".$user->getId(). " "
                    . "AND ". $qb->expr()->isNull('u_g_t.timeStop'))
            ->orderBy("g_t.sequence","ASC")   ;

        $result=NULL;
        $resultArr = $qb->getQuery()->getResult();
        if(!empty($resultArr)){
            $result =  $resultArr[0]; 
        }
        return $result;
    }
}
