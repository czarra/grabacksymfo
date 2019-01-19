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
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class GamesAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
                ->add('name', TextType::class, array('label' => 'Nazwa'))
                ->add('description', TextareaType::class, array('label' => 'Opis'))
                ->add('enabled', ChoiceType::class, array(
                    'choices' => \App\Entity\Games::getEnabledChoices(),
                     'label' => 'Aktywna'));
         
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
                ->add('name', null, array('label' => 'Nazwa'))
                ->add('code', null, array('label' => 'Kod'));
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('id');
        $listMapper
                ->add('name', TextType::class, array('label' => 'Nazwa'))
                ->add('code', TextType::class, array('label' => 'Kod'))
                ->add('description', TextareaType::class, array('label' => 'Opis'))
                ->add('enabled', TextType::class, array(
                    'label' => 'Aktywna'
                   ))
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
                ->add('name', TextType::class, array('label' => 'Nazwa'))
                ->add('code', TextType::class, array('label' => 'Kod'))
                ->add('description', TextType::class, array('label' => 'Opis'))
                ->add('enabled',TextType::class, array(
                     'label' => 'Aktywna'
                    ));
        
    }
    
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
//        $collection->remove('show');
    }
}