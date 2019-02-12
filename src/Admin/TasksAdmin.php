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

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;


class TasksAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
                ->add('name', TextType::class, array('label' => 'Nazwa'))
                ->add('longitude', NumberType::class, array('scale' => 7))
                ->add('latitude', NumberType::class, array('scale' => 7))
                ->add('description', TextareaType::class, array('label' => 'Opis'))
                ->add('file', FileType::class, [
                        'required' => false,
                        'label' => 'File (max size 2M)'
                    ])
                ->add('filename', TextType::class, array(
                    'label' => 'Nazwa pliku',
                    'required' => false,
                    'disabled'  => true,));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
                ->add('name', null, array('label' => 'Nazwa'))
                ->add('longitude')
                ->add('latitude');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('id');
        $listMapper
                ->add('name', TextType::class, array('label' => 'Nazwa'))
                ->add('longitude')
                ->add('latitude')
                ->add('description', TextareaType::class, array('label' => 'Opis'))
                 ->add('filename',TextType::class, array(
                     'label' => 'Plik'
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
                ->add('longitude')
                ->add('latitude')
                ->add('description', TextType::class, array('label' => 'Opis'))
                 ->add('filename',TextType::class, array(
                     'label' => 'Plik'
                    ));
        
    }
    
     protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
//        $collection->remove('show');
    }
    
    public function prePersist($image)
    {
        $this->manageFileUpload($image);
    }

    public function preUpdate($image)
    {
        $this->manageFileUpload($image);
    }

    private function manageFileUpload($image)
    {
        if ($image->getFile()) {
            $image->upload();
        }
    }
}