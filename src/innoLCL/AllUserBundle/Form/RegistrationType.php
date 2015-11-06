<?php
// src/innoLCL/AllUserBundle/Form/RegistrationType.php

namespace innoLCL\AllUserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('cgvaccepted', 'checkbox',[
              'label'=>'J\'ai lu les conditions de règlement du jeu' ,
              'required'=>true,
            ]);
        $builder->add('firstname', 'text', array('required' => true));
        $builder->add('lastname', 'text', array('required' => true));
        $builder->remove('username');
        $builder->add('direction', 'choice', array(
                        'choices'  => array('Ressource Humaine' => 'Ressource Humaine', 'Suivi clients' => 'Suivi clients', 'Autre' => 'Autre'),
                        'required' => true,
        ));;
    }

    public function getParent()
    {
        return 'fos_user_registration';
    }

    public function getName()
    {
        return 'innoLCL_AllUserBundle_registrationType';
    }
}

