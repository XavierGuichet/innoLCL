<?php
// src/UserBundle/Controller/RedirectController.php

namespace  innoLCL\backBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;


class RedirectController extends Controller {
    
    public function ByRoleRedirectionAction() {
        $user = $this->get('security.context')->getToken()->getUser();
        
        $tst = $user->getRoles();
        
        var_dump($tst);
        
    }

}
