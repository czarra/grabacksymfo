<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GameAdmin
 *
 * @author rad
 */
// src/Admin/CategoryAdmin.php
namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserGameAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        
//        $formMapper
//                ->add('game', null, array('required' => true,'label' => 'Gra'))
//                ->add('user', null, array('required' => true,'label' => 'User') )
//                ->add('timeStart', null, array('required' => true,'label' => 'Start'))
//                ->add('timeStop', null, array('required' => true,'label' => 'Stop'))
//                ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        
        $datagridMapper
                ->add('game',null, array('label' => 'Gra'))
                ->add('user', null, array('label' => 'User')) 
                ->add('timeStart', null, array('label' => 'Start'))
                ->add('timeStop', null, array('label' => 'Stop'));

    }

    protected function configureListFields(ListMapper $listMapper)
    {

         
        $listMapper
                ->add('game', TextType::class, array('label' => 'Gra', 'sortable' => 'game.name'))
                ->add('user', TextType::class, array('label' => 'User', 'sortable' => 'user.name'))
                ->add('timeStart', null, array('label' => 'Start'))
                ->add('timeStop', null, array('label' => 'Stop'))
                ->add('time', null, array('label' => 'Czas'))
                ->add('_action', 'actions', array('actions' => array(
                        'show' => array(),
                      //  'edit' => array(),
                      //  'delete' => array(),
                        ),'label' => 'Akcje')
                    );
        
               
    }
   
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('game', TextType::class, array('label' => 'Gra'))
            ->add('user', TextType::class, array('label' => 'User') )
            ->add('timeStart', null, array('label' => 'Start'))
            ->add('timeStop', null, array('label' => 'Stop'))
            ->add('time', null, array('label' => 'Czas'));
        
        
    }

    
    
    public function validate(ErrorElement $errorElement, $object)
    {
      
    }
    
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
        $collection->remove('edit');
        $collection->remove('create');
    }
    
    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
       // $query->addSelect("TIMEDIFF(time_stop,time_start) as timsss");
        //$query->andWhere('o.timeStop IS NOT NULL');
        return $query;
    }
    
}