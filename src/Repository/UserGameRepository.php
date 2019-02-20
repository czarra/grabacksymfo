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

use App\Entity\UserGame;
use App\Application\Sonata\UserBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
class UserGameRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserGame::class);
    }
    
    public function findAllEndGameByUser(User $user)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('u_g')
            ->from(UserGame::class, 'u_g')
            ->where("u_g.user=".$user->getId()." and u_g.timeStop IS NOT NULL")
            ->orderBy("u_g.timeStart","DESC")   ;

        return $qb->getQuery()->getResult();
    }
    
    public function findAllNotEndGameByUser(User $user)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('u_g')
            ->from(UserGame::class, 'u_g')
            ->where("u_g.user=".$user->getId()." and u_g.timeStop IS NULL")
            ->orderBy("u_g.timeStart","DESC")   ;

        return $qb->getQuery()->getResult();
    }

}
