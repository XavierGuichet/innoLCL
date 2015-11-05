<?php
// src/innoLCL/AllUserBundle/Form/RegistrationType.php

namespace innoLCL\AllUserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('cgvaccepted');
        $builder->add('firstname');
        $builder->add('lastname');
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

