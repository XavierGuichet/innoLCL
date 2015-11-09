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
                        'choices'  => array("Réseaux (Part, Pros, BP, BEGF)" => "Réseaux (Part, Pros, BP, BEGF)" ,"Fonctions supports" => "Fonctions supports", "Traitement (DSBA) et Informatiques (ITP)" => "Traitement (DSBA) et Informatiques (ITP)"),
                        'required' => true,
        ));
        $builder->add('region', 'choice', array(
                        'choices'  => array("SIEGE" => "SIEGE","IDF OUEST" => "IDF OUEST","IDF NORD" => "IDF NORD","IDF SUD" => "IDF SUD" ,"MEDITERANNEE" => "MEDITERANNEE","MIDI" => "MIDI","NORD OUEST" => "NORD OUEST","OUEST" => "OUEST","SUD OUEST" => "SUD OUEST","ANTILLES GUYANE" => "ANTILLES GUYANE","EST" => "EST","RHONE ALPES AUVERGNE" => "RHONE ALPES AUVERGNE"),
                        'required' => true,
        ));
        
        
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

