<?php
// src/UserBundle/Controller/RedirectController.php

namespace  innoLCL\AllUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;


class RedirectController extends Controller
{
    public function ByRoleRedirectionAction() {        
       $user = $this->get('security.context')->getToken()->getUser();
       $serviceBack = $this->container->get('inno_lc_lback.serviceBack');  
       $userAdminRole = null;
       
       if($serviceBack->isUserWithTooManyRole($user->getRoles())) {
            //dump("L'utilisateur à trop de role d'administration");
            $route ='fos_admin_user_security_login';
        }
        else {
            $userAdminRole = $serviceBack->getAdminRole($user->getRoles());
        }
        
        $route ='innolcl_front_homepage';
        if($userAdminRole === null) {
            if(in_array("ROLE_USER",$user->getRoles())) { $route ='innolcl_front_landing_proposal';}
        }
         if($userAdminRole == "ROLE_MODERATEUR") {
            $route ='innolcl_moderateur_list_idea';
        }
         if($userAdminRole == "ROLE_LECTEUR") {
            $route ='innolcl_lecteur_list_idea';
        }
         if($userAdminRole == "ROLE_VALIDATEUR") {
            $route ='innolcl_validateur_list_idea';
        }
         if($userAdminRole == "ROLE_SELECTIONNEUR") {
            $route ='innolcl_selectionneur_list_idea';
        }
         if($userAdminRole == "ROLE_STATS") {
            $route ='innolcl_stats_list_video';
        }
        
        $router = $this->container->get('router')->generate($route);
        $response = new RedirectResponse($router);
        return $response;
    }

}
