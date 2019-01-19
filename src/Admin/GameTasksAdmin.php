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

class GameTasksAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        
        $formMapper
                ->add('game', null, array('required' => true,'label' => 'Gra'))
                ->add('task', null, array('required' => true,'label' => 'Zadanie') )
                ->add('sequence', null, array('required' => true,'label' => 'Kolejność'));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        
        $datagridMapper
                ->add('sequence', null, array('label' => 'Kolejność'));

    }

    protected function configureListFields(ListMapper $listMapper)
    {

         
        $listMapper
                ->add('game', TextType::class, array('label' => 'Gra'))
                ->add('task', TextType::class, array('label' => 'Zadanie'))
                ->add('sequence', TextType::class, array('label' => 'Kolejność'))
                ->add('_action', 'actions', array('actions' => array(
                        'show' => array(),
                        'edit' => array(),
                        'delete' => array(),
                        ),'label' => 'Akcje')
                    );
        
               
    }
   
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            //    ->add('id')
                ->add('game', TextType::class, array('label' => 'Gra'))
                ->add('task', TextType::class, array('label' => 'Zadanie'))
                ->add('sequence', TextType::class, array('label' => 'Kolejność'));
        
        
    }

    
    
    public function validate(ErrorElement $errorElement, $object)
    {
      
    }
    
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
//        $collection->remove('show');
    }
    
    public function createQuery($context = 'list')
    {

     
        $query = parent::createQuery($context);

        return $query;
    }
    
}