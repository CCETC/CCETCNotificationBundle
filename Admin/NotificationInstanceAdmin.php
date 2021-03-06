<?php

namespace CCETC\NotificationBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;


class NotificationInstanceAdmin extends Admin
{    
    protected $entityIcon = 'icon-signal';
    
    protected $entityLabelPlural = "Notification Instances";
    
    
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
                ->add('user')
                ->add('notification')		
                ->add('needsToBeEmailed')		
                ->add('active')
                ->add('_action', 'actions', array(
                    'actions' => array(
                        'view' => array(),
                        'edit' => array(),
                        'delete' => array(),
                    ),
                    'label' => 'Actions'
                ))
        ;
    }
    
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
                ->add('user')
                ->add('notification')		
        ;
    }
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
                ->add('user')
                ->add('notification')		
                ->add('needsToBeEmailed')		
                ->add('active')		
        ;
               
    }
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
                ->add('user')
                ->add('notification')		
                ->add('needsToBeEmailed')		
                ->add('active')		
		;
    }    
}
